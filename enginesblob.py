import pandas as pd
import time
import redis
from flask import current_app
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import linear_kernel
import tfidfblob


def info(msg):
    current_app.logger.info(msg)


class ContentEngine(object):

    SIMKEY = 'p:smlr:%s'

    def __init__(self):
        self._r = redis.StrictRedis.from_url(current_app.config['REDIS_URL'])

    def train(self, data_source):
        start = time.time()
        ds = pd.read_csv(data_source)
        info("Training data ingested in %s seconds." % (time.time() - start))

        # Flush the stale training data from redis
        self._r.flushdb()

        start = time.time()
        self._train(ds)
        info("Engine trained in %s seconds." % (time.time() - start))

    def _train(self, ds):
        similar_items = tfidfblob.get_similar_articles(ds)
        for idx, row in ds.iterrows():
            self._r.zadd(self.SIMKEY % row['id'], *similar_items[idx])

    def predict(self, item_id, num):
        return self._r.zrange(self.SIMKEY % item_id, 0, num-1, withscores=True, desc=True)

    def export(self):
        f = open('model.csv','w')
        f.write('id,similar_articles\n')
        for idx in range(1,300):    
            f.write(str(idx) + ',' + ' '.join(self._r.zrange(self.SIMKEY % idx, 0, 22, withscores=False, desc=True)) + '\n')
        f.close()

content_engine = ContentEngine()
