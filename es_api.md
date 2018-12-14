curl -XGET 'http://localhost:9200/_count?pretty' -d '
{
    "query": {
        "match_all": {}
    }
}

curl --user elastic:Myelastic#2018 -XGET 'es-cn-0pp0w6pct000q7sca.public.elasticsearch.aliyuncs.com:9200'

#所有index
curl --user elastic:Myelastic#2018 -XGET 'es-cn-0pp0w6pct000q7sca.public.elasticsearch.aliyuncs.com:9200/_cat/indices'

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

#mapping
GET companys/_mapping/

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











