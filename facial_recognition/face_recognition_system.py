import cv2
import numpy as np
import face_recognition
import os
import json
import pickle
from datetime import datetime
import sqlite3
import base64
import sys

class FacialRecognitionSystem:
    def __init__(self, database_path=None):
        # Ensure DB lives alongside this script for consistency across callers
        if database_path is None:
            script_dir = os.path.dirname(os.path.abspath(__file__))
            database_path = os.path.join(script_dir, 'face_database.db')
        self.database_path = database_path
        self.known_face_encodings = []
        self.known_face_names = []
        self.face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
        self.initialize_database()
        self.load_known_faces()
    
    def initialize_database(self):
        """Initialize SQLite database for storing face data"""
        conn = sqlite3.connect(self.database_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS faces (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                name TEXT NOT NULL,
                face_encoding BLOB,
                image_path TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                success BOOLEAN,
                confidence REAL,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def load_known_faces(self):
        """Load known faces from database"""
        conn = sqlite3.connect(self.database_path)
        cursor = conn.cursor()
        
        cursor.execute("SELECT name, face_encoding FROM faces")
        faces = cursor.fetchall()
        
        for face in faces:
            name, encoding_blob = face
            encoding = pickle.loads(encoding_blob)
            self.known_face_encodings.append(encoding)
            self.known_face_names.append(name)
        
        conn.close()
    
    def register_face(self, image_path, name, user_id=None):
        """Register a new face in the system"""
        try:
            # Load image
            image = face_recognition.load_image_file(image_path)
            # Detect face locations and compute encoding using the detected box to improve robustness
            face_locations = face_recognition.face_locations(image, model='hog')
            if len(face_locations) == 0:
                return {"success": False, "message": "No face detected in the image"}
            # If multiple faces, pick the largest (most likely primary subject)
            if len(face_locations) > 1:
                # Select by area (w*h)
                def box_area(box):
                    top, right, bottom, left = box
                    return max(0, bottom - top) * max(0, right - left)
                face_locations.sort(key=box_area, reverse=True)
                # Informative message but still proceed with the largest face
                selected_box = face_locations[0]
            else:
                selected_box = face_locations[0]

            face_encodings = face_recognition.face_encodings(image, known_face_locations=[selected_box])
            if len(face_encodings) == 0:
                return {"success": False, "message": "Unable to compute face encoding"}

            if len(face_locations) > 1:
                return {"success": False, "message": "Multiple faces detected. Please use an image with only one face"}
            
            face_encoding = face_encodings[0]
            
            # Save to database
            conn = sqlite3.connect(self.database_path)
            cursor = conn.cursor()
            
            cursor.execute(
                "INSERT INTO faces (user_id, name, face_encoding, image_path) VALUES (?, ?, ?, ?)",
                (user_id, name, pickle.dumps(face_encoding), image_path)
            )
            
            conn.commit()
            conn.close()
            
            # Add to current session
            self.known_face_encodings.append(face_encoding)
            self.known_face_names.append(name)
            
            return {"success": True, "message": f"Face registered successfully for {name}"}
            
        except Exception as e:
            return {"success": False, "message": f"Error registering face: {str(e)}"}
    
    def recognize_face(self, image_path, confidence_threshold=0.6, tolerance=None):
        """Recognize a face from an image.
        - confidence_threshold kept for backward-compat but we use tolerance if provided.
        - face_recognition typical tolerance is 0.6 (lower is stricter).
        """
        try:
            # Load image
            image = face_recognition.load_image_file(image_path)
            face_locations = face_recognition.face_locations(image, model='hog')
            if len(face_locations) == 0:
                return {"success": False, "message": "No face detected in the image"}
            # Use the largest detected face for recognition
            def box_area(box):
                top, right, bottom, left = box
                return max(0, bottom - top) * max(0, right - left)
            face_locations.sort(key=box_area, reverse=True)
            primary_box = face_locations[0]
            face_encodings = face_recognition.face_encodings(image, known_face_locations=[primary_box])
            
            if len(face_encodings) == 0:
                return {"success": False, "message": "Unable to compute face encoding"}
            
            face_encoding = face_encodings[0]
            
            # Compare with known faces
            # Determine tolerance to use
            if tolerance is None:
                # Map legacy confidence_threshold (0..1) to face_recognition tolerance (default ~0.6)
                # If confidence_threshold provided (e.g., 0.6), use that directly as tolerance if in a sane range
                if confidence_threshold and 0.2 <= float(confidence_threshold) <= 0.8:
                    tolerance_to_use = float(confidence_threshold)
                else:
                    tolerance_to_use = 0.6
            else:
                tolerance_to_use = float(tolerance)

            face_distances = face_recognition.face_distance(self.known_face_encodings, face_encoding)
            
            if len(face_distances) > 0:
                best_match_index = np.argmin(face_distances)
                best_distance = float(face_distances[best_match_index])
                is_match = best_distance <= tolerance_to_use
                # Derive a similarity score in [0,1] from distance for UI purposes
                # Typical distances range ~0.3-0.8, map 0.6 as boundary
                similarity = max(0.0, min(1.0, (0.6 - best_distance) / 0.6 + 0.5)) if best_distance <= 0.6 else max(0.0, 1.0 - (best_distance - 0.6) / 0.4)

                if is_match:
                    recognized_name = self.known_face_names[best_match_index]
                    
                    # Log the attempt
                    self.log_login_attempt(recognized_name, True, similarity)
                    
                    return {
                        "success": True,
                        "name": recognized_name,
                        "distance": best_distance,
                        "similarity": similarity,
                        "tolerance": tolerance_to_use,
                        "message": f"Face recognized as {recognized_name} (distance {best_distance:.3f})"
                    }
            
            # Log failed attempt
            self.log_login_attempt(None, False, 0)
            
            return {"success": False, "message": "Face not recognized"}
            
        except Exception as e:
            return {"success": False, "message": f"Error recognizing face: {str(e)}"}
    
    def log_login_attempt(self, user_id, success, confidence):
        """Log login attempts for security monitoring"""
        conn = sqlite3.connect(self.database_path)
        cursor = conn.cursor()
        
        cursor.execute(
            "INSERT INTO login_attempts (user_id, success, confidence) VALUES (?, ?, ?)",
            (user_id, success, confidence)
        )
        
        conn.commit()
        conn.close()
    
    def capture_face_from_camera(self, output_path="captured_face.jpg"):
        """Capture face from webcam"""
        cap = cv2.VideoCapture(0)
        
        if not cap.isOpened():
            return {"success": False, "message": "Could not open camera"}
        
        face_detected = False
        captured_image = None
        
        while True:
            ret, frame = cap.read()
            if not ret:
                break
            
            # Use face_recognition for better detection
            rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            fr_boxes = face_recognition.face_locations(rgb_frame)
            faces = []
            for (top, right, bottom, left) in fr_boxes:
                faces.append((left, top, right-left, bottom-top))
                cv2.rectangle(frame, (left, top), (right, bottom), (255, 0, 0), 2)
                face_detected = True
            
            # Display instructions
            cv2.putText(frame, "Press SPACE to capture, ESC to cancel", (10, 30), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 0), 2)
            
            cv2.imshow('Face Capture', frame)
            
            key = cv2.waitKey(1) & 0xFF
            if key == 27:  # ESC
                break
            elif key == 32 and face_detected:  # SPACE
                captured_image = frame.copy()
                break
        
        cap.release()
        cv2.destroyAllWindows()
        
        if captured_image is not None:
            # If a face was detected in the last frame, crop the largest face
            rgb_captured = cv2.cvtColor(captured_image, cv2.COLOR_BGR2RGB)
            boxes = face_recognition.face_locations(rgb_captured)
            if boxes:
                # choose largest
                def area(b):
                    t, r, btm, l = b
                    return max(0, btm - t) * max(0, r - l)
                boxes.sort(key=area, reverse=True)
                t, r, btm, l = boxes[0]
                face_img = captured_image[t:btm, l:r]
                if face_img.size != 0:
                    captured_image = face_img
            cv2.imwrite(output_path, captured_image)
            return {"success": True, "path": output_path, "message": "Face captured successfully"}
        else:
            return {"success": False, "message": "No face captured"}
    
    def get_registered_faces(self):
        """Get list of all registered faces"""
        conn = sqlite3.connect(self.database_path)
        cursor = conn.cursor()
        
        cursor.execute("SELECT id, user_id, name, image_path, created_at FROM faces")
        faces = cursor.fetchall()
        
        conn.close()
        
        return [
            {
                "id": face[0],
                "user_id": face[1],
                "name": face[2],
                "image_path": face[3],
                "created_at": face[4]
            }
            for face in faces
        ]
    
    def delete_face(self, face_id):
        """Delete a registered face"""
        conn = sqlite3.connect(self.database_path)
        cursor = conn.cursor()
        
        cursor.execute("DELETE FROM faces WHERE id = ?", (face_id,))
        conn.commit()
        conn.close()
        
        # Reload faces
        self.known_face_encodings = []
        self.known_face_names = []
        self.load_known_faces()
        
        return {"success": True, "message": "Face deleted successfully"}

# API endpoints for PHP integration
def handle_api_request(request_data):
    """Handle API requests from PHP"""
    system = FacialRecognitionSystem()
    
    action = request_data.get('action')
    
    if action == 'register':
        image_path = request_data.get('image_path')
        name = request_data.get('name')
        user_id = request_data.get('user_id')
        return system.register_face(image_path, name, user_id)
    
    elif action == 'recognize':
        image_path = request_data.get('image_path')
        confidence_threshold = request_data.get('confidence_threshold', 0.6)
        return system.recognize_face(image_path, confidence_threshold)
    
    elif action == 'capture':
        output_path = request_data.get('output_path', 'captured_face.jpg')
        return system.capture_face_from_camera(output_path)
    
    elif action == 'list_faces':
        return {"success": True, "faces": system.get_registered_faces()}
    
    elif action == 'list_attempts':
        try:
            limit = int(request_data.get('limit', 10))
        except Exception:
            limit = 10
        # Query recent login attempts
        conn = sqlite3.connect(system.database_path)
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id, user_id, success, confidence, timestamp
            FROM login_attempts
            ORDER BY id DESC
            LIMIT ?
        """, (limit,))
        rows = cursor.fetchall()
        conn.close()
        attempts = []
        for r in rows:
            attempts.append({
                "id": r[0],
                "user": r[1],  # note: name string is stored here by recognize_face
                "success": bool(r[2]),
                "confidence": float(r[3]) if r[3] is not None else None,
                "timestamp": r[4]
            })
        return {"success": True, "attempts": attempts}
    
    elif action == 'delete_face':
        face_id = request_data.get('face_id')
        return system.delete_face(face_id)
    
    else:
        return {"success": False, "message": "Invalid action"}

if __name__ == "__main__":
    # CLI entrypoint for PHP integration: expects a single JSON argument
    try:
        if len(sys.argv) >= 2:
            request_json = sys.argv[1]
            try:
                request_data = json.loads(request_json)
            except Exception:
                # If the arg is a path to a JSON file
                if os.path.exists(request_json):
                    with open(request_json, 'r', encoding='utf-8') as f:
                        request_data = json.load(f)
                else:
                    raise
            response = handle_api_request(request_data)
            print(json.dumps(response))
        else:
            # No args: simple health check
            system = FacialRecognitionSystem()
            print(json.dumps({"success": True, "message": "Facial Recognition System initialized", "faces_loaded": len(system.known_face_names)}))
    except Exception as e:
        print(json.dumps({"success": False, "message": f"CLI error: {str(e)}"}))
