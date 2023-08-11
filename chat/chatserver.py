import os #check file exits
#search sentence
from sentence_transformers import SentenceTransformer
import torch
import torch.nn.functional as F
import pandas as pd

#import openai

from bardapi import Bard

os.environ['_BARD_API_KEY']="YwiaqJJz7_hVm9Q1jXCrYSNTymC965zIj_IoKfgInQv9u3TYFCSZw6r17w-fNwf9HkHi4Q."

import nltk

#translate
from googletrans import Translator
from langdetect import detect

#flask
from flask import *


try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    print("Nltk hasn't satisfied yet.")
    nltk.download('punkt')
    pass
else:
    print("Nltk has already satisfied.")
    pass


#文章をベクトルに変換して類似度を出す

model = SentenceTransformer('all-MiniLM-L6-v2')


#Flaskオブジェクトの生成
app = Flask(__name__)


@app.route("/chat", methods=["GET", "POST"])
def main():
    global model


    if request.method == "GET":
        return "Error:couldn't get a question text."
    

    #user_id
    user_id = str(request.form["user"])

    #input
    question_text = str(request.form["q"])


    question_lang = detect(question_text)

    #質問文を英語に翻訳
    translator = Translator()
    question_text = translator.translate(question_text, src=question_lang, dest='en').text



    #質問の答えが書かれているファイルを検索
    path = "../search/"+user_id+".csv"
    is_file = os.path.isfile(path)
    if is_file == False:
        exit()
        pass


    search_csv_list = []

    with open("../search/"+user_id+".csv",encoding="utf-8") as f:
        for line in f:
           search_csv_list.append(line.replace("\n","").split("($split)"))
           pass
        pass

    #回答がついているquestionも参考にする
    path = "../question/"+user_id+".csv"
    is_file = os.path.isfile(path)
    if is_file == False:
        exit()
        pass


    with open("../question/"+user_id+".csv",encoding="utf-8") as f:
        for line in f:
            split_line = line.replace("\n","").split("($split)")
            if len(split_line) >= 4:
                if split_line[3] == "1":
                    search_csv_list.append(split_line)
                    pass
                pass
            pass
        pass



    #タイトルで検索して、上位3つのページを参考にする

    #search sentence
    search_sentence = [question_text]

    #Our sentences we like to encode

    search_file_list = [i[0] for i in search_csv_list]


    #検索するテキストを英語に翻訳
    md_lang_search = detect(search_file_list[0])

    if md_lang_search != "en":
        for i in range(len(search_file_list)):
            translator = Translator()
            text_after = translator.translate(search_file_list[i], src=md_lang_search, dest='en').text
            search_file_list[i] = text_after
            pass
        pass



    #Sentences are encoded by calling model.encode()
    embedding = model.encode(search_file_list)

    #vector of searched content
    vecs = torch.from_numpy(embedding)

    #vector of search sentence
    search_vec = torch.from_numpy(model.encode(search_sentence))

    #ベクトルから検索文との類似度をもとめる
    sim = F.cosine_similarity(search_vec[0], vecs).tolist()
    result = pd.DataFrame({'sentence': search_file_list, 'similarity': sim})

    print("search sentence: "+search_sentence[0])
    print(result)


    search_csv_list_tmp = []

    for i in search_csv_list:
        search_csv_list_tmp.append(i)
        pass


    result_file_id = []

    result_file_title = []

    #上から3つ類似度が高いものを取る
    for i in range(3):

        if len(sim) == 1:
            break
            pass

        sim_max_num = max(sim)

        for j in range(len(search_csv_list_tmp)):
            if sim[j] == sim_max_num and len(search_csv_list_tmp[j]) >= 2:
                result_file_id.append(search_csv_list_tmp[j][1])
                result_file_title.append(search_csv_list_tmp[j][0])
                sim.pop(j)
                search_csv_list_tmp.pop(j)
                break
                pass
            pass
        pass


    print(result_file_id)



    search_document = ""

    for i in range(len(result_file_id)):

        print(result_file_id[i])

        #すべてのファイルひとつの文字列にまとめる
        with open("../data/"+result_file_id[i]+".md",encoding="utf-8") as f:

            #このファイルのコンテンツ
            search_document_part = ""

            read_text = f.read()

            #改行などをすべて取ったやつ
            rep_text = read_text.replace(" ","").replace("\n","")

            if rep_text != "":
                if rep_text[0] == "#" and rep_text[1] != "#":
                    search_document_part += read_text
                    pass
                else:
                    search_document_part += "\n # "+result_file_title[i]+"\n"+read_text
                    pass
                pass
            
            #改行で区切ったやつ
            search_document_part_list = search_document_part.split("\n")

            search_document_part = ""

            for j in range(len(search_document_part_list)):
                part_tmp = search_document_part_list[j]
                if len(part_tmp.replace(" ","").replace("\n","")) != 0:
                    if part_tmp.replace(" ","").replace("\n","")[0] == "#" and part_tmp.replace(" ","").replace("\n","")[1] != "#":
                        search_document_part += search_document_part_list[j]+"<a class='from_link' href='view.php?u="+user_id+"&fid="+result_file_id[i]+"'></a>\n"
                        pass
                    else:
                        search_document_part += search_document_part_list[j]+"\n"
                        pass
                    pass
                pass
            
            search_document_part += "\n"

            search_document += search_document_part

            
            f.close()
            pass
        pass


    #print(search_document)


    #検索
    #それをタイトルと一緒にsearch_documentにぶち込む(タイトルはh1としてぶち込む 内容がなかったらぶち込まない)


    #markdownを解析
    #内容ごとに分ける
    document_list = [] #markdownを構文解析したもの


    search_document = search_document.replace("<br>","\n")

    document_list_tmp = search_document.split("\n")


    document_content_tmp = []


    for i in range(len(document_list_tmp)):
        if document_list_tmp[i].find("# ") != -1 and document_list_tmp[i].find("##") == -1:
            if document_content_tmp != []:
                document_list.append(document_content_tmp)
                pass
            document_content_tmp = []
            pass
        elif i == len(document_list_tmp)-1:
            document_content_tmp.append(document_list_tmp[i])
            if document_content_tmp != []:
                document_list.append(document_content_tmp)
                pass
            document_content_tmp = []
            break
            pass

        if document_list_tmp[i] != "":
            document_content_tmp.append(document_list_tmp[i])
            pass

        pass


    #markdownのタイトルの#を消す
    document_list_tmp = document_list

    for i in range(len(document_list)):
        document_list_tmp[i][0] = document_list_tmp[i][0].replace("#","")
        pass

    #markdownの色々を消す
    for i in range(len(document_list)):
        for j in range(len(document_list_tmp[i])):
            p_tmp = document_list_tmp[i][j]
            p_tmp = p_tmp.replace("###","")
            p_tmp = p_tmp.replace("##","")
            p_tmp = p_tmp.replace("**","")
            document_list_tmp[i][j] = p_tmp
            pass
        pass

    document_list = document_list_tmp

    print(document_list)


    #markdownの特殊文字を削除

    #htmlタグや、画像などを消す
    
    for i in range(len(document_list)):

        delete_mode = False
        url_mode = False
        for j in range(len(document_list[i])):
            list_line = ""
            for k in range(len(document_list[i][j])):
                 #url以外のhtmlタグを削除
                if document_list[i][j][k] == "<" and document_list[i][j][k:].find(">") != -1 and len(document_list[i][j]) != k-1:
                    if document_list[i][j][k+1] != "a" and len(document_list[i][j]) != k-2:
                        if document_list[i][j][k+2] == "a":
                            if document_list[i][j][k+1] != "/":
                                delete_mode = True
                                pass
                            pass
                        else:
                            delete_mode = True
                        pass
                    pass

                if delete_mode == False and document_list[i][j][k] != "!":
                    list_line += document_list[i][j][k]
                    pass
                pass

                if document_list[i][j][k] == ">":
                    delete_mode = False
                    pass

                if document_list[i][j][k] == ")" and url_mode == True:
                    delete_mode = False
                    pass

            document_list[i][j] = list_line

        pass

    #print(document_list)


    document_list_tmp = []

    for i in document_list:

        line_tmp = []
        for j in i:
            if j != "":
                line_tmp.append(j)
                pass
            pass

        if line_tmp != [""] or line_tmp != []:
            document_list_tmp.append(line_tmp)
            pass
        pass

    document_list = document_list_tmp


    #ファイルの中から回答を検索
    #search sentence
    search_sentence = [question_text]

    #Our sentences we like to encode

    sentences_tmp = [i[0] for i in document_list]

    sentences = []

    #追加したhtmlタグ(不要な文字)を削除
    for i in range(len(sentences_tmp)):
        if sentences_tmp[i].find("<a class='from_link'") != -1:
            sentences.append(sentences_tmp[i][:sentences_tmp[i].find("<a class='from_link'")])
            pass
        pass
    
    print(sentences)



    #Sentences are encoded by calling model.encode()
    embedding = model.encode(sentences)

    #vector of searched content
    vecs = torch.from_numpy(embedding)

    #vector of search sentence
    search_vec = torch.from_numpy(model.encode(search_sentence))

    #ベクトルから検索文との類似度をもとめる
    sim = F.cosine_similarity(search_vec[0], vecs).tolist()
    result = pd.DataFrame({'sentence': sentences, 'similarity': sim})


    result_num = 0

    #一番類似度が高かったものを選ぶ
    sim_max_num = max(sim)

    print("sim_max_num")
    print(result)
    print(sim_max_num)

    if sim_max_num < 0.5:
        resp = make_response("I'm sorry but I couldn't find any answer.")
        resp.headers['Access-Control-Allow-Origin'] = '*'
        return resp

    #内容を表示
    for i in range(len(sim)):
        if sim[i] == sim_max_num:
            result_num = i
            pass
        pass

    result_md = ""

    for i in document_list[result_num][1:]:
        result_md += i+"\n"
        pass


    resuslt_title = document_list[result_num][0]




    #result
    #内容の類似度も調べるかもしれない

    

    question = '''
    Doc: \n
    '''+result_md+'''\n\n

    Message : According to the doc, '''+question_text+'''\n\n

    It should be whith in '''+str(request.form["w"])+''' words.

    '''

    response = Bard().get_answer(question)['content']

    response = translator.translate(response, src='en', dest=question_lang).text

    return_sentence = "### "+resuslt_title+"\n\n"

    
    return_sentence += response+"\n"
    

    resp = make_response(return_sentence)
    resp.headers['Access-Control-Allow-Origin'] = '*'

    return resp

@app.route("/mode/<mode>", methods=["GET", "POST"])
def wirte_help(mode):

    #user_id
    user_id = str(request.form["user"])

    #input
    question_text = str(request.form["q"])


    if mode == "write":
        question = '''
        Input: \n
        '''+question_text+'''\n\n

        Write a document with reference to the Input in markdown.
        It should be in English.
        '''
        pass
    if mode == "talk":
        question = '''
        Input: \n
        '''+question_text+'''\n\n

        Write things what I want to talk with reference to the Input.
        It should be in English.
        '''
        pass

    response = Bard().get_answer(question)['content']

    response = translator.translate(response, src='en', dest=detect(question_text)).text

    
    resp = make_response(response)
    resp.headers['Access-Control-Allow-Origin'] = '*'

    return resp


app.run(port=5000)
