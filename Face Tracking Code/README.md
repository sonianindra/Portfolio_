Face Tracking Code
Description:
This project uses OpenCV and Python to track human faces in real-time using a webcam or video feed. It detects faces using Template Matching and tracks movement across frames.

Tech Stack:
Python
OpenCV
Jupyter Notebook

Features:
Real-time face detection and bounding box visualization
Lightweight and runs locally without a GPU
Adjustable parameters for tuning detection accuracy

Use Case:
Great for understanding basic computer vision tasks and building more complex applications like emotion detection or attendance via facial recognition.

Overview:
" This project is a real time face tracking system using a template matching algorithm in openCV

The template was a grayscale image of the target face since color isn’t necessary for spatial template matching and would only add noise so instead of rgb values it's just one channel with the intensity value per pixel. And In general I chose template matching instead of Haar of CNN to understand how low level vision operations work and see how pattern similarity is computed pixel by pixel 

I also resize the template image to 10% of the original size as a trade off to boost speed once the webcam stream is in process. I preprocessed with canny edge detection to prepare the code for edge based features like shape matching which would be important here and for the matching there are two modes, initial detection and tracking mode, so the initial searches the entire frame and the tracking searches a small region near the last match for optimization. I also did this matching using normalized squared differences so that it would be a little less sensitive to lighting conditions and movements ,so in this case the lowest score corresponds to the best match for the bounding box to be displayed at. Once the object is detected I just limited the next search to a nearby region of interest instead of the full frame 

Overall the core concepts for me was how to optimize for latency which is why i chose the ROI search and also i felt that template matching using the same ideas for signal detection and also teaching me the importance of noise handling and tracking over time."