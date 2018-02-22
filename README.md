Magento2 Extensions
-------------------- 

1. Hp_CategorySeo  - Google Seo Pagination to add Following code into head tag to improve google indexing.

    <link  rel="canonical" href="https://example.com/catagorty.html?p=2" />
    <link  rel="prev" href="https://example.com/catagorty.html?p=1" />
    <link  rel="next" href="https://example.com/catagorty?p=3" />


    Installation stesps :

    1. Download zip file of extension and extract that file.
    2. Paste Extension directory in to app/code. 
    3. Run command :  php bin/magento setup:upgrade
    4. Clear cache :  php bin/magento cache:clean
    5. Flush cache :  php bin/magento cache:flush
    6. Static content deploy : php  bin/magento setup:static-content:deploy


