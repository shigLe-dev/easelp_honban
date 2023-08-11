import os #check file exits
#search sentence
from sentence_transformers import SentenceTransformer
import torch
import torch.nn.functional as F
import pandas as pd

#summarize answer
import sumy
from sumy.parsers.plaintext import PlaintextParser
from sumy.nlp.tokenizers import Tokenizer
from sumy.summarizers.lex_rank import LexRankSummarizer

import nltk


nltk.download('punkt')


#文章をベクトルに変換して類似度を出す

model = SentenceTransformer('all-MiniLM-L6-v2')


#user_id
user_id = "user2"

#input
question_text = "What is python."



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




#search sentence
search_sentence = [question_text]

#Our sentences we like to encode

search_file_list = [i[0] for i in search_csv_list]



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

for i in range(3):

    if len(sim) == 1:
        break
        pass

    sim_max_num = max(sim)

    for j in range(len(search_csv_list)):
        if sim[j] == sim_max_num:
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

    with open("../data/"+result_file_id[i]+".md",encoding="utf-8") as f:

        read_text = f.read()

        rep_text = read_text.replace(" ","").replace("\n","")

        if rep_text != "":
            if rep_text[0] == "#":
                search_document += read_text
                pass
            else:
                search_document += "\n # "+result_file_title[i]+"\n"+read_text
                pass
            pass
        f.close()
        pass
    pass


print("search_document")
print(search_document)


#検索
#それをタイトルと一緒にsearch_documentにぶち込む(タイトルはh1としてぶち込む 内容がなかったらぶち込まない)


#markdownを解析
#内容ごとに分ける
document_list = [] #markdownを構文解析したもの

document_list_tmp = search_document.split("\n")

print(document_list_tmp)

document_content_tmp = []


for i in range(len(document_list_tmp)):
    if document_list_tmp[i].find("# ") != -1:
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

md_delete_list = ["###","##","#","> ","**"]

for i in range(len(document_list)):
    for md_del in md_delete_list:
        document_list_tmp[i][0] = document_list_tmp[i][0].replace(md_del,"")
        pass
    pass

document_list = document_list_tmp


#markdownの特殊文字を削除

#htmlタグや、画像などを消す
for i in range(len(document_list)):

    delete_mode = False
    url_mode = False
    for j in range(len(document_list[i])):
        list_line = ""
        for k in range(len(document_list[i][j])):
            if document_list[i][j][k] == "<" and document_list[i][j][k:].find(">") != -1:
                delete_mode = True
                pass
            
            if document_list[i][j][k] == "[" and document_list[i][j][k:].find("]") != -1:
                back_p = document_list[i][j][k:].find("]")
                if back_p < len(document_list[i][j])-1:
                    if document_list[i][j][back_p+1] == "(":
                        if document_list[i][j][back_p+1:].find(")"):
                            url_mode = True
                            delete_mode = True
                            pass
                        pass
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

print("document_list")
print(document_list)

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

print(document_list)






#ファイルの中から回答を検索
#search sentence
search_sentence = [question_text]

#Our sentences we like to encode

sentences = [i[0] for i in document_list]



#Sentences are encoded by calling model.encode()
embedding = model.encode(sentences)

#vector of searched content
vecs = torch.from_numpy(embedding)

#vector of search sentence
search_vec = torch.from_numpy(model.encode(search_sentence))

#ベクトルから検索文との類似度をもとめる
sim = F.cosine_similarity(search_vec[0], vecs).tolist()
result = pd.DataFrame({'sentence': sentences, 'similarity': sim})

print("search sentence: "+search_sentence[0])
print(result)


result_num = 0

#一番類似度が高かったものを選ぶ
sim_max_num = max(sim)

#内容を表示
for i in range(len(sim)):
    if sim[i] == sim_max_num:
        result_num = i
        pass
    pass

result_md = ""

for i in document_list[result_num]:
    result_md += i+"\n"
    pass


#result
#内容の類似度も調べるかもしれない
print("result:")
print(result_md)




# For Strings
parser = PlaintextParser.from_string(result_md,Tokenizer("english"))

summarizer = LexRankSummarizer()
#Summarize the document with 2 sentences
summary = summarizer(parser.document, 2)


answer_sentence = ""

for sentence in summary:
    print(sentence)
    answer_sentence += sentence+"\n"
    pass

print(answer_sentence)