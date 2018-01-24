# -*- coding: utf-8 -*-
import scrapy
from scrapy.spiders import CrawlSpider, Rule
from scrapy.linkextractors import LinkExtractor
from tribunnews.items import TribunnewsItem

class NewsSpider(CrawlSpider):
    name = 'news'
    allowed_domains = ['www.tribunnews.com']
    start_urls = ['http://www.tribunnews.com/seleb/2018/01/16']

    rules = (Rule(LinkExtractor(allow=(), restrict_css=('.paging',)),
             callback="parse_item",
             follow=True),)

    def parse_item(self, response):
        item_links = response.css('.ptb15 > .fbo.f16').extract()
        for a in item_links:
            b = a.split('"')[3] + '?page=all'
            yield scrapy.Request(b, callback=self.parse_detail_page)

    def parse_detail_page(self, response):
        title = response.css('#arttitle::text').extract()[0].strip()
        category = response.url.split('/')[3]
        content_tmp = response.css('.txt-article > p ::text').extract() 
        date = response.css('.mt10 > time.grey ::text').extract()[0] 
        item = TribunnewsItem()
        item['title'] = title
        item['category'] = category
        item['content'] = '\n'.join(content_tmp) 
        item['date'] = date
        item['url'] = response.url 
        yield item 
