{% include 'ObjectHeader.tpl' %}
<<
/Length {{ length }}
/Filter /{{ filter }}
/Length1 {{ length1 }}
>>
stream
{{ stream|raw }}
endstream
{% include 'ObjectFooter.tpl' %}