# COMPREHENSIVE STUDENT-FACULTY PERFORMANCE EVALUATION WITH SENTIMENT ANALYSIS


Welcome to the **Comprehensive Student-Faculty Performance Evaluation with Sentiment Analysis** project. 

## Features

This project provides a comprehensive platform for managing and evaluating student and faculty performance, incorporating sentiment analysis for deeper insights.

*   **Role-Based Access Control:**
    *   **Admin:** Manages academic years, departments, users (admins, deans, faculty, students), classes, subjects, criteria, and evaluation restrictions. Can view evaluation reports and sentiment analysis results.
    *   **Dean:** Manages faculty within their department, views reports specific to their department.
    *   **Faculty:** Views their assigned classes and subjects, can view evaluation results concerning them.
    *   **Student (Regular & Irregular):** Can log in, view assigned subjects/faculty, and submit performance evaluations.
*   **Academic Setup:**
    *   Manage Academic Years and set a default active year.
    *   Manage Departments.
*   **User Management:**
    *   Add, edit, and delete Admin, Dean, Faculty, and Student users.
    *   Support for both **Regular** and **Irregular** students.
    *   Assign subjects individually to Irregular students.
    *   Batch upload functionality for students.
*   **Class and Subject Management:**
    *   Define Curriculums, Levels, and Sections to create Classes.
    *   Manage a list of Subjects with codes.
*   **Evaluation Criteria:**
    *   Define evaluation criteria with adjustable order.
    *   Create specific evaluation questions linked to criteria.
*   **Evaluation Process:**
    *   Set up Evaluation Restrictions, linking faculty, subjects, and classes for a specific academic year.
    *   Students evaluate faculty based on defined criteria and questions.
    *   Evaluation status tracking (evaluated/not evaluated).
*   **Reporting and Analysis:**
    *   View detailed evaluation reports per faculty, filterable by class and subject.
    *   **Sentiment Analysis:** Automatically analyzes student comments using TextBlob to determine sentiment (positive, negative, neutral), providing qualitative insights alongside quantitative scores.
    *   Visual display of evaluation averages and sentiment distribution.
*   **Profile Management:** Users can update their profile information and password.

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
```

#### 4. Configure PHP Scripts

You need to configure the PHP scripts to point to the correct Python executable and the sentiment analysis script.

1. **Locate the Files:**
    - Open `ajax.php` and `student/evaluate.php` files in your project directory.

2. **Search for Placeholder Comments:**
    - Look for the comment `//To be changed` in both files.

3. **Update Paths:**
    - Replace the placeholder paths with the actual paths on your system.

4. **Correct Paths:**
    - Go to cmd and type "where python" to know that path for your python executable.

##### Example Configuration:

```php
<?php
// Path to the Python executable
$pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe'; //To be changed

// Path to the sentiment analysis script
$scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; // To be changed
?>
