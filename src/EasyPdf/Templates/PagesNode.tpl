{% include 'ObjectHeader.tpl' %}
<<
/Type /Pages
/Kids [{% for key, page in kids %} {{ page.getIndirectReference() }} {% endfor %}]
/Count {{ numberPage }}
>>
{% include 'ObjectFooter.tpl' %}