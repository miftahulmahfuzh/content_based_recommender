import scrapy

class TribunnewsItem(scrapy.Item):
    title = scrapy.Field()
    category = scrapy.Field()
    content = scrapy.Field()
    date = scrapy.Field()
    url = scrapy.Field()
