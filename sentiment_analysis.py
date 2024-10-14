# sentiment_analysis.py
import sys
import json
from textblob import TextBlob

def analyze_sentiment(text):
    analysis = TextBlob(text)
    polarity = analysis.sentiment.polarity  # Ranges from -1 to 1
    subjectivity = analysis.sentiment.subjectivity  # Ranges from 0 to 1

    # Calculate positive and negative percentages
    positive_percentage = max(0, polarity) * 100  # Positive polarity scaled to 0-100%
    negative_percentage = max(0, -polarity) * 100  # Negative polarity scaled to 0-100%

    if polarity > 0:
        sentiment = 'Positive'
    elif polarity == 0:
        sentiment = 'Neutral'
    else:
        sentiment = 'Negative'

    return {
        'sentiment': sentiment,
        'polarity': polarity,
        'subjectivity': subjectivity,
        'positive_percentage': positive_percentage,
        'negative_percentage': negative_percentage
    }

if __name__ == "__main__":
    # Read input text from command line arguments
    input_text = sys.argv[1]
    result = analyze_sentiment(input_text)
    print(json.dumps(result))
