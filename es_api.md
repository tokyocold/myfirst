curl -XGET 'http://localhost:9200/_count?pretty' -d '
{
    "query": {
        "match_all": {}
    }
}

curl --user elastic:Myelastic#2018 -XGET 'es-cn-0pp0w6pct000q7sca.public.elasticsearch.aliyuncs.com:9200'

#所有index
curl --user elastic:Myelastic#2018 -XGET 'es-cn-0pp0w6pct000q7sca.public.elasticsearch.aliyuncs.com:9200/_cat/indices'

GET _cat/indices 

#query
curl --user elastic:Myelastic#2018 -XGET 'es-cn-0pp0w6pct000q7sca.public.elasticsearch.aliyuncs.com:9200/aqsc/_search?pretty'
##空查询
    GET /_search
    {}
等价于
    GET /_search
    {
        "query": {
            "match_all": {} //结构:
        }
    }

match不能放多个条件,可以使用bool条件组合查询


#bool查询
bool查询必须结合子句: must filter should must_not
形式:
            'query' => [
                'bool'=>[
                    'must' => [
                    ],
                    'filter'=>[
                    ],
                    'must_not'=>[
                    ],
                ]
            ]
下面查询是等价的:
{
    "match": {
        "title": {
            "query":    "brown fox",
            "operator": "and"  //等价与must 
        }
    }
}
{
  "bool": {
    "must": [
      { "term": { "title": "brown" }},
      { "term": { "title": "fox"   }}
    ]
  }
}

#精确值 范围
term:{   "price" : 20    }
"terms" : {                     "price" : [20, 30]                }
##范围
"range" : {
    "price" : {
        "gte" : 20,
        "lte" : 40
    }
}




##查询表达式
结构:
{
    QUERY_NAME: {
        ARGUMENT: VALUE,
        ARGUMENT: VALUE,...
    }
}

QUERY_NAME: bool , match_all, match, range, term, 


eg:
    "match": {
      "name": "runba"
    }
    "term": {
      "name": "runba"
    }
    //name='runba' and tel!='1234567'`
    "bool": {
      "must": [
        {"match": {
          "name": "runba"
        }}
      ],
      "must_not": [
        {"match": {
          "tel": "1234567"
        }}
      ]
    }




#doc
PUT 'companys/company/1' -d '
 {
     "name": "runba",
     "addr": "shanghai",
     "tel": 1234567
 }'

GET 'companys/company/1' 

##deletebyquery
根据条件删除必须要用_delete_by_query API .  不是用DELETE

POST csaqsc/meeting/_delete_by_query
{
  "query": {
    "terms": {
      "address_id": [
        1
      ]
    }
  }
}

##局部更新
POST /website/blog/1/_update
{
   "doc" : {
      "tags" : [ "testing" ],
      "views": 0
   }
}


#权限
##xpack
查看权限
GET _xpack/security/_authenticate
GET /_xpack/security/user
GET /_xpack/security/role


新增用户
POST /_xpack/security/user/jacknich
{
  "password" : "j@rV1s",
  "roles" : [ "admin", "other_role1" ],   //角色 
  "full_name" : "Jack Nicholson",
  "email" : "jacknich@example.com",
  "metadata" : {
    "intelligence" : 7
  }
}


















#mapping
GET companys/_mapping/

##修改mapping
可以新增type到index或者新增field到type,不能修改存在字段的mapping.
追加field到已有的index mapping
PUT tax_test
{
  "mappings":{
      "company":{
        "properties": {
          "name":{
            "type": "text"
          },
          "id:":{
            "type": "long"
          }
        }
    }
  }
}

//追加
PUT tax_test/_mapping/company
{
          "properties": {
          "address":{
            "type": "text"
          }
        }
}








##父子mapping

父type可以和子type同时创建,但不允许先创建父type再创建子type.

 获取子文档时,必须加上routing
error:get companys/employee/2
correct: get companys/employee/2?parent=1
PUT companys
{
  "mappings": {
    "company": {},
    "employee":{
      "_parent": {
        "type": "company"
      }
    }
  }
}
##根据子文档查询
GET companys/_search
{
  "query": {
    "has_child": {
      "type": "employee",
      "query": {
        "match": {
          "name": "Ash"
        }
      }
    }
  }
}

## 孙文档问题
父文档->子文档->孙文档
在索引文档的时候,如果没有routing属性,则routing默认为parent_id的值
在没有routing时
在索引子文档的时候,routing为父文档ID
在索引孙文档的时候,routing为子文档ID

但孙文档routing必须为父文档的ID,否则无法建立父子,子孙关系,因此,在索引孙文档时,一定要加routing参数






#nested 对象
修改存在字段的mapping是不允许的.
在已经存在的type中增加一个字段:
PUT companys/_mapping/employee/
{
  "properties": {
    "dep_nested":{
      "type": "nested"
    }
  },
    "_parent": {    //注意:父子结构的文档中,修改mapping的同时也要指定_parent(bug??)
    "type": "company"
  }
}

nested对象主要是用来处理Array,讲Array里每一个item看做一个独立的文档.
  "dep":[{
    "name":"A","formal":"yes"
  },{
    "name":"B","formal":"no"
  }],  //object
    "dep_nested":[{
    "name":"A","formal":"yes"
  },{
    "name":"B","formal":"no"
  }]  //nested
##查询
    "bool": {
      "must": [
        {"match":{"dep.formal":"A"}},
        {"match":{"dep.name":"no"}}
      ]
    }

对object对象执行会查出结果,然而这个并不是我们期望的
    "bool": {
      "must": [
        {"match":{"dep_nested.formal":"A"}},
        {"match":{"dep_nested.name":"no"}}
      ]
    }
对nested查询无结果,正确.




#查询返回 certain fields  
通过 _source
{
    "_source": ["user", "message", ...],
    "query": ...,
    "size": ...
}


#聚合
##buckets嵌套
  "aggs": {
    "company_id": {
          "terms": {
            "field": "company_id",
            "size": 10
          },
      "aggs":{
          "top_record":{
            "top_hits": {
              "_source": {
                "includes": ["company_id","zone_company_id","year_month"] #返回的字段
              }, 
              "sort": [{"year_month": {"order": "asc"}}],    #bucktes中的记录的排序
              "size": 1 #每个bucktes返回的记录数
            }
          },
          "max_year_month": {
                "max": {
                  "field": "year_month"
                }
            },
          "min_year_month": {
                "min": {
                  "field": "year_month"
                }
        }
      }
    }
  }

在company_id 这个buckets下,嵌套了一个新的agg,里面包含了三个bucket
"top_record","min_year_month","max_year_month"

##  返回每个buckets的一条记录
使用 top_hits 获取
          "top_record":{
            "top_hits": {
              "_source": {
                "includes": ["company_id","zone_company_id","year_month"]
              }, 
              "sort": [{"year_month": {"order": "asc"}}], 
              "size": 1
            }
          },


## 去重总数  count distnct company_id
使用  cardinality
    "aggs": {
        "company_id": {
            "cardinality": {
                "field": "company_id"
            }
        }
    }

##f 分页 
    "aggs": {
        "company_id": {
            "terms": {
                "order": {
                  "_count": "desc"
                }, 
                "field": "company_id" , 
                "include": {
                    "partition": 0, 
                    "num_partitions": 1
                }, 
                "size": 1003
            }
        }
    }

##实现having查询
使用 bucket_selector

    "min_year_filter": {
        "bucket_selector": {
            "buckets_path": {
                "min_year": "min_year_month"
            }, 
            "script": "params.min_year>=1072886400&&params.min_year<=1075564800"
        }
    }


## aggs 聚合始终是在query的结果集上进一步聚合的.


#权限
xpack插件
GET /_xpack/security/_authenticate   //系统用户
GET /_xpack/security/user       //用户列表
GET /_xpack/security/role       //角色列表


#setting
PUT /csaqsc_bulk/_settings
{
 "index" : {
    "refresh_interval" : "1800s"//刷新时间
    "refresh_interval" : null //刷新时间 默认值

  }
}
GET /csaqsc_bulk/_settings

#重建索引
如果有字段更新只能通过重建索引的形式.
流程:
1 建新索引(新结构)
2 reindex(重建索引)
POST _reindex
{
  "source": {
    "index": "csaqsc_master",
     "type": ["meeting"],//只reindex meeting这个type
    "_source":["company_id"]//只reindex  company_id这个字段
    "query": {
      "term": {
        "user": "kimchy"   //还可以查询屌不屌
      }
    }

  },
  "dest": {
    "index": "csaqsc_master_emergencydisposal"
  },
  "script": {
    "inline": "if (ctx._type!='EmergencyDisposal') ctx.op = 'delete'",    //可以用script更进一步控制,等同于source里面的type:['EmergencyDisposal'],只转这个type.
    "lang": "painless"
  }
  
}
3 删除旧索引
4 建立别名 
post _aliases
{
  "actions": [
        { "add" : { "index" : "index_b", "alias" : "index_a" } }
    ]
}









