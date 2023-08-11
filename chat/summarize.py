import os #check file exits
#summarize
import sumy
from sumy.parsers.plaintext import PlaintextParser
from sumy.nlp.tokenizers import Tokenizer
from sumy.summarizers.lex_rank import LexRankSummarizer

from flask import *

#言語を特定
from langdetect import detect

import nltk

try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    print("Nltk hasn't satisfied yet.")
    nltk.download('punkt')
    pass
else:
    print("Nltk has already satisfied.")
    pass



app = Flask(__name__)


@app.route('/summarize',methods=["GET","POST"])
def hello_world():

    if request.method == "GET":
        return "Error:couldn't get a question text."


    original_md = str(request.form["md"])

    #言語を特定
    md_lang = detect(original_md)

    if md_lang == "ja":
        language = "japanese"
        pass
    elif md_lang == "en":
        language = "english"
        pass


    # For Strings
    parser = PlaintextParser.from_string(''.join(original_md).replace("<","").replace(">",""), Tokenizer(language))

    summarizer = LexRankSummarizer()
    #Summarize the document with 3 sentences
    summary = summarizer(parser.document, 4)

    answer_sentence = ""

    for s in summary:
        answer_sentence += str(s) + "\n"
        pass

    print(answer_sentence)

    resp = make_response(answer_sentence)
    resp.headers['Access-Control-Allow-Origin'] = '*'

    return resp


app.run(port=3000)