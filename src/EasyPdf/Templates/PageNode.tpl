{% include 'ObjectHeader.tpl' %}
<<
/Type /Page
/Parent {{ parent }}
/MediaBox [{% for key, size in mediaBox %} {{ size }} {% endfor %}]
/Resources {{ resources }}
/Contents [{% for key, c in contents %} {{ c.getIndirectReference() }} {% endfor %}]
>>
{% include 'ObjectFooter.tpl' %}