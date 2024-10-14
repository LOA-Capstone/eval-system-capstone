# COMPREHENSIVE STUDENT-FACULTY PERFORMANCE EVALUATION WITH SENTIMENT ANALYSIS


Welcome to the **Comprehensive Student-Faculty Performance Evaluation with Sentiment Analysis** project. 


## Setup and Installation

### Prerequisites

Before setting up the project, ensure you have the following installed on your system:

1. **XAMPP:** A free and open-source cross-platform web server solution stack package.
2. **Python 3.x:** Programming language used for sentiment analysis.
3. **Git:** Version control system for tracking changes.
4. **Composer (optional):** Dependency manager for PHP.

### Installation Steps

#### 1. Install XAMPP

XAMPP provides an easy way to install Apache, MySQL, PHP, and other essential tools.

- **Download XAMPP:** [Download Link](https://www.apachefriends.org/index.html)
- **Installation:**
  - Run the installer and follow the on-screen instructions.
  - Choose the components you need (ensure Apache and MySQL are selected).
  - Complete the installation and start the Apache and MySQL services via the XAMPP Control Panel.

#### 2. Install Python

Ensure Python 3.x is installed on your system.

- **Download Python:** [Download Link](https://www.python.org/downloads/)
- **Installation:**
  - Run the installer.
  - **Important:** Check the box "Add Python to PATH" before clicking "Install Now."

#### 3. Install Required Python Libraries

The sentiment analysis script relies on the `TextBlob` library. Follow these steps to install it:



   ```bash
   pip3 install textblob
```
   ```bash
   python -m textblob.download_corpora

