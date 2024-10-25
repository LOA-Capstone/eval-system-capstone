# sentiment_analysis.py
import sys
import json
from textblob import TextBlob

def analyze_sentiment(text):
    analysis = TextBlob(text)
    polarity = analysis.sentiment.polarity  # Ranges from -1 to 1
    subjectivity = analysis.sentiment.subjectivity  # Ranges from 0 to 1

    # Normalize polarity to 0-1
    normalized_polarity = (polarity + 1) / 2  # Now ranges from 0 to 1

    score = normalized_polarity  # For clarity

    # Assign sentiment label based on normalized polarity
    if 0.0 <= score < 0.1:
        sentiment = 'Very Strong (Negative)'
    elif 0.1 <= score < 0.3:
        sentiment = 'Strong (Negative)'
    elif 0.3 <= score < 0.4:
        sentiment = 'Moderate (Negative)'
    elif 0.4 <= score < 0.6:
        sentiment = 'Neutral'
    elif 0.6 <= score < 0.7:
        sentiment = 'Moderate (Positive)'
    elif 0.7 <= score < 0.9:
        sentiment = 'Strong (Positive)'
    elif 0.9 <= score <= 1.0:
        sentiment = 'Very Strong (Positive)'
    else:
        sentiment = 'Unknown'

    # Assign subjectivity label based on subjectivity score
    subj_score = subjectivity  # Ranges from 0 to 1

    if 0.0 <= subj_score < 0.10:
        subjectivity_label = 'Highly Objective'
    elif 0.10 <= subj_score < 0.30:
        subjectivity_label = 'Objective'
    elif 0.30 <= subj_score < 0.45:
        subjectivity_label = 'Slightly Objective'
    elif 0.45 <= subj_score < 0.55:
        subjectivity_label = 'Neutral'
    elif 0.55 <= subj_score < 0.70:
        subjectivity_label = 'Slightly Subjective'
    elif 0.70 <= subj_score < 0.85:
        subjectivity_label = 'Subjective'
    elif 0.85 <= subj_score <= 1.0:
        subjectivity_label = 'Highly Subjective'
    else:
        subjectivity_label = 'Unknown'

    return {
        'sentiment': sentiment,
        'score': score,
        'subjectivity': subjectivity,
        'subjectivity_label': subjectivity_label
    }

if __name__ == "__main__":
    # Read input text from command line arguments
    input_text = ' '.join(sys.argv[1:])
    if input_text.startswith("'") and input_text.endswith("'"):
        input_text = input_text[1:-1]
    result = analyze_sentiment(input_text)
    print(json.dumps(result))
