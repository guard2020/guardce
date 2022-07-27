import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
import os, sys, re, csv, collections, time, itertools, pickle
#ml methods and metrics
from sklearn.neural_network import MLPClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.neighbors import KNeighborsClassifier
from sklearn.ensemble import RandomForestClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn import svm, tree
from sklearn.tree import DecisionTreeClassifier
from sklearn.model_selection import GridSearchCV
from sklearn import preprocessing
import sklearn.metrics as metrics
from sklearn.metrics import accuracy_score, balanced_accuracy_score, confusion_matrix, f1_score, recall_score, precision_score
from sklearn.preprocessing import StandardScaler
from skfeature.function.similarity_based import fisher_score
from sklearn.feature_selection import SelectKBest, chi2, f_classif, mutual_info_classif, f_regression
from sklearn.model_selection import train_test_split
from sklearn.svm import LinearSVC
from imblearn.under_sampling import RandomUnderSampler
from joblib import dump, load
from datetime import datetime
sys.path.append(r'/home/odnan/anaconda3/lib/python3.7/site-packages/')
import tensorflow as tf
from tensorflow import keras
from sklearn.calibration import CalibratedClassifierCV
from itertools import groupby
from sklearn.calibration import CalibratedClassifierCV
from sklearn.metrics import classification_report
from sklearn.metrics import plot_confusion_matrix
from imblearn.over_sampling import SMOTE, RandomOverSampler

start_time = time.time()
#cols = ['IN_BYTES', 'IN_PKTS', 'OUT_BYTES', 'OUT_PKTS', 'FLOW_DURATION_MICROSECONDS', 'LONGEST_FLOW_PKT', 'SHORTEST_FLOW_PKT']

cols = ['IN_BYTES', 'IN_PKTS', 'OUT_BYTES', 'OUT_PKTS', 'MIN_IP_PKT_LEN', 'MAX_IP_PKT_LEN', 'FLOW_ACTIVE_TIMEOUT', 'FLOW_INACTIVE_TIMEOUT',
        'TOTAL_BYTES_EXP', 'TOTAL_PKTS_EXP', 'TOTAL_FLOWS_EXP', 'MIN_TTL', 'MAX_TTL', 'PACKET_SECTION_OFFSET', 'SAMPLED_PACKET_SIZE',
        'FLOW_DURATION_MICROSECONDS', 'SAMPLING_SIZE', 'FRAME_LENGTH', 'PACKETS_OBSERVED', 'PACKETS_SELECTED', 'SRC_FRAGMENTS', 'DST_FRAGMENTS',
        'CLIENT_NW_LATENCY_MS', 'SERVER_NW_LATENCY_MS', 'SRC_TO_DST_MAX_THROUGHPUT',
        'SRC_TO_DST_MIN_THROUGHPUT', 'SRC_TO_DST_AVG_THROUGHPUT', 'DST_TO_SRC_MAX_THROUGHPUT', 'DST_TO_SRC_MIN_THROUGHPUT', 'DST_TO_SRC_AVG_THROUGHPUT',
        'NUM_PKTS_UP_TO_128_BYTES', 'NUM_PKTS_128_TO_256_BYTES', 'NUM_PKTS_256_TO_512_BYTES', 'NUM_PKTS_512_TO_1024_BYTES', 'NUM_PKTS_1024_TO_1514_BYTES',
        'NUM_PKTS_OVER_1514_BYTES', 'LONGEST_FLOW_PKT', 'SHORTEST_FLOW_PKT', 'RETRANSMITTED_IN_BYTES', 'RETRANSMITTED_IN_PKTS', 'RETRANSMITTED_OUT_BYTES',
        'RETRANSMITTED_OUT_PKTS', 'OOORDER_IN_PKTS', 'OOORDER_OUT_PKTS',  'NUM_PKTS_TTL_EQ_1', 'NUM_PKTS_TTL_2_5', 'NUM_PKTS_TTL_5_32',
        'NUM_PKTS_TTL_32_64', 'NUM_PKTS_TTL_64_96', 'NUM_PKTS_TTL_96_128', 'NUM_PKTS_TTL_128_160', 'NUM_PKTS_TTL_160_192', 'NUM_PKTS_TTL_192_224',
        'NUM_PKTS_TTL_224_255', 'DURATION_IN', 'DURATION_OUT', 'TCP_WIN_MIN_IN', 'TCP_WIN_MAX_IN', 'TCP_WIN_MSS_IN', 'TCP_WIN_SCALE_IN', 'TCP_WIN_MIN_OUT',
        'TCP_WIN_MAX_OUT', 'TCP_WIN_MSS_OUT', 'TCP_WIN_SCALE_OUT']
##
##'TCP_FLAGS', 'IPV4_SRC_MASK', 'INPUT_SNMP', 'IPV4_DST_MASK', 'OUTPUT_SNMP', 'IPV6_SRC_MASK', 'IPV6_DST_MASK','UNTUNNELED_PROTOCOL','SAMPLING_INTERVAL',
##  'ENTROPY_CLIENT_BYTES', 'ENTROPY_SERVER_BYTES', 'APPL_LATENCY_MS', 'CLIENT_TCP_FLAGS', 'SERVER_TCP_FLAGS',

##cols = ['IN_BYTES', 'IN_PKTS', 'TCP_FLAGS', 'IPV4_SRC_MASK', 'INPUT_SNMP', 'IPV4_DST_MASK', 'OUTPUT_SNMP', 'OUT_BYTES',
##        'OUT_PKTS', 'MIN_IP_PKT_LEN', 'MAX_IP_PKT_LEN', 'IPV6_SRC_MASK', 'IPV6_DST_MASK', 'SAMPLING_INTERVAL', 'FLOW_ACTIVE_TIMEOUT', 'FLOW_INACTIVE_TIMEOUT',
##        'TOTAL_BYTES_EXP', 'TOTAL_PKTS_EXP', 'TOTAL_FLOWS_EXP', 'MIN_TTL', 'MAX_TTL', 'PACKET_SECTION_OFFSET', 'SAMPLED_PACKET_SIZE',
##        'FLOW_DURATION_MICROSECONDS', 'SAMPLING_SIZE', 'FRAME_LENGTH', 'PACKETS_OBSERVED', 'PACKETS_SELECTED', 'SRC_FRAGMENTS', 'DST_FRAGMENTS',
##        'CLIENT_NW_LATENCY_MS', 'SERVER_NW_LATENCY_MS', 'CLIENT_TCP_FLAGS', 'SERVER_TCP_FLAGS', 'APPL_LATENCY_MS', 'SRC_TO_DST_MAX_THROUGHPUT',
##        'SRC_TO_DST_MIN_THROUGHPUT', 'SRC_TO_DST_AVG_THROUGHPUT', 'DST_TO_SRC_MAX_THROUGHPUT', 'DST_TO_SRC_MIN_THROUGHPUT', 'DST_TO_SRC_AVG_THROUGHPUT',
##        'NUM_PKTS_UP_TO_128_BYTES', 'NUM_PKTS_128_TO_256_BYTES', 'NUM_PKTS_256_TO_512_BYTES', 'NUM_PKTS_512_TO_1024_BYTES', 'NUM_PKTS_1024_TO_1514_BYTES',
##        'NUM_PKTS_OVER_1514_BYTES', 'LONGEST_FLOW_PKT', 'SHORTEST_FLOW_PKT', 'RETRANSMITTED_IN_BYTES', 'RETRANSMITTED_IN_PKTS', 'RETRANSMITTED_OUT_BYTES',
##        'RETRANSMITTED_OUT_PKTS', 'OOORDER_IN_PKTS', 'OOORDER_OUT_PKTS', 'UNTUNNELED_PROTOCOL', 'NUM_PKTS_TTL_EQ_1', 'NUM_PKTS_TTL_2_5', 'NUM_PKTS_TTL_5_32',
##        'NUM_PKTS_TTL_32_64', 'NUM_PKTS_TTL_64_96', 'NUM_PKTS_TTL_96_128', 'NUM_PKTS_TTL_128_160', 'NUM_PKTS_TTL_160_192', 'NUM_PKTS_TTL_192_224',
##        'NUM_PKTS_TTL_224_255', 'DURATION_IN', 'DURATION_OUT', 'TCP_WIN_MIN_IN', 'TCP_WIN_MAX_IN', 'TCP_WIN_MSS_IN', 'TCP_WIN_SCALE_IN', 'TCP_WIN_MIN_OUT',
##        'TCP_WIN_MAX_OUT', 'TCP_WIN_MSS_OUT', 'TCP_WIN_SCALE_OUT',  'ENTROPY_CLIENT_BYTES', 'ENTROPY_SERVER_BYTES']


def get_data(name, address, attackers, victim, reverse, label): 
        files = sorted([f for f in os.listdir(address)])
        flen = len(files)
        extract = [sorted(c, key = len) for c in [list(i) for j, i in groupby(files, key=lambda a: re.findall('^\d+', a)[0])]]
        files = [j for sub in extract for j in sub]
        if len(files)!=flen:
                print("one of the files were not read correctly")
                sys.exit()
        for f in files:
                if f == files[0]:
                        df = pd.read_csv(address+f, sep='|', dtype = str)
                else:
                        df2 = pd.read_csv(address+f, sep='|', dtype = str)
                        df = pd.concat([df, df2])
                if df.shape[0] >= sample_limit:
                        df = df[:sample_limit]
                        break
        df=df.reset_index(drop=True)
        df[cols] = df[cols].apply(pd.to_numeric)
        df["Label"]=[0] * len(df)
        df.loc[((df['IPV4_SRC_ADDR'].isin(attackers)) & (df['IPV4_DST_ADDR'].isin(victim))), 'Label'] = label
        if reverse:
                df.loc[((df['IPV4_SRC_ADDR'].isin(victim)) & (df['IPV4_DST_ADDR'].isin(attackers))), 'Label'] = label
        df = df[cols+['Label']]
        print(collections.Counter(df["Label"]))
        return df

##set sample limit per attack
sample_limit = 25000

class_names = {0:"Normal", 1:"LOIC TCP", 2: "Hulk", 3: "GoldenEye", 4: "Slowloris", 5: "SlowHTTP", 6: "LOIC HTTP", 7: "HOIC TCP", 8:"LOIC UDP", 9: "DNS",
               10: "LDAP", 11: "MSSQL", 12: "NTP", 13: "NetBIOS", 14: "SNMP", 15: "SSDP", 16: "UDP", 17: "SYN", 18: "TFTP", 19: "UDP_Lag", 20: "WebDDoS"}
dump(class_names, 'class_names.joblib')

#2017
attacker = ['172.16.0.1']
victim = ['192.168.10.50']
LOIC_TCP_2017 = get_data('LOIC_TCP_2017', '2017/LOIC/data/', attacker, victim, False, 1)       
Hulk_2017 = get_data('Hulk_2017', '2017/Hulk/data/', attacker, victim, False, 2)
GoldenEye_2017 = get_data('GoldenEye_2017', '2017/GoldenEye/data/', attacker, victim, False, 3)
slowloris_2017 = get_data('slowloris_2017', '2017/Slowloris/data/', attacker, victim, False, 4)
SlowHTTPtest_2017 = get_data('SlowHTTPtest_2017', '2017/Slowhttp/data/', attacker, victim, False, 5)

#2018
attackers = ["18.218.115.60", "18.219.9.1", "18.219.32.43", "18.218.55.126", "52.14.136.135",
             "18.219.5.43", "18.216.200.189", "18.218.229.235", "18.218.11.51", "18.216.24.42"]
LOIC_HTTP_2018 = get_data('LOIC_HTTP_2018', '2018/LOIC_HTTP/data/', attackers, ['172.31.69.25'], False, 6)
HOIC_TCP_2018 = get_data('HOIC_TCP_2018', '2018/HOIC_TCP/data/', attackers, ['172.31.69.28'], False, 7)
LOIC_UDP_2018 = get_data('LOIC_UDP_2018', '2018/LOIC_UDP/data/', attackers, ['172.31.69.28'], False, 8)
GoldenEye_2018 = get_data('GoldenEye_2018', '2018/GoldenEye/data/', ['18.219.211.138'], ['172.31.69.25'], False, 3)
Slowloris_2018 = get_data('Slowloris_2018', '2018/Slowloris/data/', ['18.217.165.70'], ['172.31.69.25'], False, 4)
Hulk_2018 = get_data('Hulk_2018', '2018/Hulk/data/', ['18.219.193.20'], ['172.31.69.25'], False, 2)
SlowHTTPTest_2018 = get_data('SlowHTTPTest_2018', '2018/Slowhttp/data/', ['13.59.126.31'], ['172.31.69.25'], False, 5)

#2019
source = ['172.16.0.5']
destination = ['192.168.50.1']

DNS = get_data('DNS', '2019/0191-0196_DNS/data/', source, destination, True, 9)
#DNS = get_data('DNS', '2019/0191-0378_DNS2/data/', source, destination, True, 9)
LDAP = get_data('LDAP', '2019/0378-0439_LDAP/data/', source, destination, True, 10)
MSSQL = get_data('MSSQL', '2019/0443-0467_MSSQL/data/', source, destination, True, 11)
NTP = get_data('NTP', '2019/0-0188_NTP/data/', source, destination, True, 12)
NetBIOS = get_data('NetBIOS', '2019/0474-0485_NetBIOS/data/', source, destination, True, 13)
SNMP = get_data('SNMP', '2019/0486-0569_SNMP/data/', source, destination, True, 14)
SSDP = get_data('SSDP', '2019/0571-0592_SSDP/data/', source, destination, True, 15)
UDP = get_data('UDP', '2019/0592-0617_UDP/data/', source, destination, True, 16)
SYN = get_data('SYN', '2019/0617-0619_SYN/data/', source, destination, True, 17)
TFTP = get_data('TFTP', '2019/0620-0818_TFTP/data/', source, destination, True, 18)
UDP_lag = get_data('UDP_lag', '2019/0617-0617_UDP_LAG/data/', source, destination, True, 19)
WebDDoS = get_data('WebDDoS', '2019/0617-0617_WebDDoS/data/', source, destination, True, 20)


Hulk = pd.concat([Hulk_2017, Hulk_2018]).sample(n=sample_limit, random_state=0)
df = pd.concat([LOIC_TCP_2017, slowloris_2017, SlowHTTPtest_2017, LOIC_HTTP_2018, HOIC_TCP_2018, GoldenEye_2017, Hulk, 
                LOIC_UDP_2018, GoldenEye_2018, Slowloris_2018, SlowHTTPTest_2018, DNS, LDAP, MSSQL, NTP,
                NetBIOS, SNMP, SSDP, UDP, SYN, TFTP, UDP_lag, WebDDoS])

##remove columns without change (clean)
print(df.shape)
df=df.loc[:, (df != df.iloc[0]).any()] 
print(df.shape)

print(collections.Counter(df["Label"]))
train, test, train_target, test_target = train_test_split(df.drop(columns=['Label']), df['Label'], random_state=0)

#standarize to evaluate only the columns
scaler = StandardScaler()
train2 = pd.DataFrame(scaler.fit_transform(train), columns = train.columns)

##feature selection settings ANOVA
b1 = SelectKBest(f_classif, k='all')
b1.fit(train2, train_target)
X_selected=b1.transform(train2)
print("check whether no NANs", b1.scores_)
score_index1=b1.scores_.argsort()[::-1]
sorter=train2.columns[score_index1][0:20]#[0:len(cols)]
#apply to the current train and test
train=train[sorter]
test=test[sorter]

print(sorter)
#standarize again with new number of columns so that there will just be 1 exported joblib
scaler = StandardScaler()
train = pd.DataFrame(scaler.fit_transform(train), columns = train.columns)
test = pd.DataFrame(scaler.transform(test), columns = test.columns)

##balance training set
##train, train_target = RandomOverSampler(random_state=0).fit_resample(train, train_target) #SMOTE()
##print(collections.Counter(train_target))



dump(train.columns, 'columns.joblib')
dump(scaler, 'scaler.joblib') 

columns = list(train.columns)
train = np.asarray(train)
train_target = np.asarray(train_target.astype('int'))
test = np.asarray(test)
test_target = np.asarray(test_target.astype('int'))

def combined_function(fname):
        if (fname == "svm"):
                #clf_params = {'kernel':('linear', 'rbf', 'sigmoid'), 'C':[0.001, 0.01, 0.1, 1, 10, 100], 'gamma' :[0.001, 0.01, 0.1, 1, 10, 100]}
                #clf = svm.SVC(random_state=0, probability=True)
                clf_params = {'base_estimator__C':[0.001, 0.01, 0.1, 1, 10, 100]}
                clf = CalibratedClassifierCV(base_estimator=LinearSVC(random_state=0))
         
        if (fname == "rf"):
                #clf_params = {'n_estimators': range(10,101,10), 'max_depth' : range(1, 21), 'criterion' :['gini', 'entropy']}
                clf_params = {'n_estimators': [100], 'max_depth': [30], 'criterion' :['gini']} #
                clf = RandomForestClassifier(random_state=0)#, n_estimators = 50, max_depth = 20, criterion = 'gini')
        if (fname == "nb"):
                clf_params = {}
                clf = GaussianNB()
        if (fname == "mlp"):
                clf_params = {'solver': ['lbfgs', 'adam' , 'sgd'],'activation': ["logistic", "relu", "tanh"],
                              'hidden_layer_sizes': [(train.shape[1],)]}
                clf = MLPClassifier(random_state=0)
        if (fname == "mlp2"):
                clf_params = {'solver': ['lbfgs', 'adam' , 'sgd'],'activation': ["logistic", "relu", "tanh"],
                              'hidden_layer_sizes': [(20,), (40,), (60,), (80,), (100,)]}
                clf = MLPClassifier(random_state=0)
        if (fname == "dt"):
                #clf_params = {'criterion':['gini','entropy'], 'max_depth': range(1, 21)}
                clf_params = {'criterion':['gini']}
                clf = DecisionTreeClassifier(random_state=0)
        if (fname == "knn"):
                clf_params = {'n_neighbors': range(10,101, 10), 'metric' : ["euclidean", "manhattan", "chebyshev", "minkowski"]}
                clf = KNeighborsClassifier()
        if (fname == "lr"):
                clf_params = {'penalty': ['l1', 'l2'], 'C' : [0.001, 0.01, 0.1, 1, 10, 100]}
                clf = LogisticRegression(random_state=0)
        if (fname == "lr2"):
                clf_params = {'penalty': ['l1', 'l2'], 'C' : [0.001, 0.01, 0.1, 1, 10, 100]}
                clf = LogisticRegression(solver='liblinear', random_state=0)

        if (fname == "nn"):
                model = tf.keras.Sequential([
                        tf.keras.layers.Dense(train.shape[1], activation=tf.nn.relu, input_shape=(train.shape[1],)),  # input shape required
                        tf.keras.layers.Dense(train.shape[1], activation=tf.nn.relu),
                        tf.keras.layers.Dense(len(set(train_target)), activation=tf.nn.softmax)])
                print(model.summary())

                lr_schedule = keras.optimizers.schedules.ExponentialDecay(
                        initial_learning_rate=0.001,
                        decay_steps=10000,
                        decay_rate=0.9)
                optimizer_decay = keras.optimizers.SGD(learning_rate=lr_schedule)
                
                model.compile(loss='sparse_categorical_crossentropy',
                              #optimizer= tf.keras.optimizers.Adam(learning_rate=0.001), #optimizer_decay,#
                              #metrics=[tf.keras.metrics.Precision(), tf.keras.metrics.Recall()])
                              metrics=['accuracy'])
                epochs = 10
                model_train=model.fit(train, train_target, epochs=epochs, validation_split = 0.1, batch_size=64)  
                test_loss, test_acc2 = model.evaluate(test,  test_target, verbose=2)
                model.save('test_nnmodelmulticlass')
                test_preds= model.predict_classes(test)
                params=[]        

        else:
                grid_clf_acc = GridSearchCV(clf, param_grid =clf_params, scoring = 'accuracy', verbose=10, n_jobs=30)#, return_train_score=True)
                grid_clf_acc.fit(train, train_target)
                dump(grid_clf_acc, fname+'model_multiclass_new.joblib', compress=1)
                #Predict values based on new parameters
                preds = grid_clf_acc.predict_proba(test)
                test_preds= grid_clf_acc.predict(test)
                preds = preds[:,1]
                le=list(grid_clf_acc.cv_results_['params'][grid_clf_acc.best_index_].values())        
                params = [le, grid_clf_acc.best_score_]

        print(test_preds)
    
        accuracy = accuracy_score(test_target, test_preds) 
        fscore = f1_score(test_target, test_preds, average='micro')
        precision = precision_score(test_target, test_preds, average='micro')
        recall = recall_score(test_target, test_preds, average='micro')
        bal_accuracy = balanced_accuracy_score(test_target, test_preds)

        print(fname + " accuracy", accuracy)
        print(fname + " balanced accuracy", bal_accuracy)
        print(fname + " F1", fscore)
        print("precision:", precision, "recall:", recall)
        confy = confusion_matrix(test_target, test_preds)
        print(confy)

        print(classification_report(test_target, test_preds)) #, target_names=['Class 1', 'Class 2', 'Class 3']
        if (fname != "nn"):
                row=[fname, accuracy, fscore, precision, recall, params, columns]
        else:
                row=[fname, accuracy, fscore, precision, recall, epochs, params, columns]
            

        with open('test_multiclass.csv', 'a') as csvFile:
                writer = csv.writer(csvFile)
                writer.writerow(row)
                csvFile.close()

##    if (np.isnan(preds).any()== False):        
##        fpr, tpr, threshold = metrics.roc_curve(test_target, preds)
##        roc_auc = metrics.auc(fpr, tpr)   
##        row_roc = [list(fpr), list(tpr), list(threshold), roc_auc]       
##    else:
##        row_roc = [[], [], [], []]
##    with open(fname+'_rocs', 'a') as csvFile:
##        writer = csv.writer(csvFile)
##        writer.writerow(row_roc)
##    csvFile.close()


        if (fname == "nn"):
                # list all data in history
                print(model_train.history.keys())
                dump(model_train.history, 'trainhistory') 
                # summarize history for accuracy
                plt.plot(model_train.history['accuracy'])
                plt.plot(model_train.history['val_accuracy'])
                plt.title('model accuracy')
                plt.ylabel('accuracy')
                plt.xlabel('epoch')
                plt.legend(['training', 'validation'], loc='upper left')
                plt.savefig('nn_accuracy.eps', format='eps', quality=100,bbox_inches='tight')
                plt.clf()
                #plt.show()
                # summarize history for loss
                plt.plot(model_train.history['loss'])
                plt.plot(model_train.history['val_loss'])
                plt.title('model loss')
                plt.ylabel('loss')
                plt.xlabel('epoch')
                plt.legend(['training', 'validation'], loc='upper left')
                plt.savefig('nn_loss.eps', format='eps', quality=100,bbox_inches='tight') 
                #plt.show()

##    
##    np.set_printoptions(precision=2)
##
##    # Plot non-normalized confusion matrix
##    titles_options = [("Confusion matrix, without normalization", None),
##                      ("Normalized confusion matrix", 'true')]
##    for title, normalize in titles_options:
##        disp = plot_confusion_matrix(grid_clf_acc, test, test_target, cmap=plt.cm.Blues, normalize=normalize) #display_labels=class_names,
##        disp.ax_.set_title(title)
##
##        print(title)
##        print(disp.confusion_matrix)
##    plt.savefig('confs.eps', format='eps', quality=100,bbox_inches='tight') 
##    plt.show()


                np.set_printoptions(precision=2)

                # Plot non-normalized confusion matrix
                plt.figure()
                plot_confusion_matrix(confy, classes=range(0,21), normalize = False,
                                      title='Confusion matrix, without normalization')
                plt.savefig('confs_'+str(epochs)+'.eps', format='eps', quality=100,bbox_inches='tight')
                #plt.show()

                plt.figure()
                plot_confusion_matrix(confy, classes=range(0,21), normalize = True,
                                      title='Confusion matrix, with normalization')
                plt.savefig('confs2_'+str(epochs)+'.eps', format='eps', quality=100,bbox_inches='tight')
                #plt.show()






def plot_confusion_matrix(cm, classes, normalize, title='Confusion matrix', cmap=plt.cm.Blues):
        """This function prints and plots the confusion matrix. Normalization can be applied by setting `normalize=True."""
        if normalize:
                cm = cm.astype('float') * 100 / cm.sum(axis=1)[:, np.newaxis]
                print("Normalized confusion matrix")
        else:
                print('Confusion matrix, without normalization')

        print(cm)
        plt.imshow(cm, interpolation='nearest', cmap=cmap)
        plt.title(title)
        plt.colorbar()
        tick_marks = np.arange(len(classes))
        plt.xticks(tick_marks, classes, rotation=45)
        plt.yticks(tick_marks, classes)

        fmt = '.0f' if normalize else 'd'
        thresh = cm.max() / 2.
        for i, j in itertools.product(range(cm.shape[0]), range(cm.shape[1])):
                plt.text(j, i, format(cm[i, j], fmt),
                         horizontalalignment="center",
                         color="white" if cm[i, j] > thresh else "black")

        plt.ylabel('True label')
        plt.xlabel('Predicted label')
        plt.tight_layout()

##combined_function('mlp2')
##combined_function('lr2')
##combined_function('dt')
##combined_function('nb')
##combined_function('lr')
##combined_function('mlp')
##combined_function('svm')
##combined_function('knn')

combined_function('rf')        
#combined_function('nn')
print(time.time() - start_time)
