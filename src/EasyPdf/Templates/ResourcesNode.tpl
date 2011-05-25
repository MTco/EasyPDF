{% include 'ObjectHeader.tpl' %}
<<
/ProcSet [/PDF /Text]
/Font
<<
{% for key, font in fonts %}
/F{{ font.getIndex() }} {{ font.getIndirectReference() }}
{% endfor %}
>>
>>
{% include 'ObjectFooter.tpl' %}