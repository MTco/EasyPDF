{% include 'ObjectHeader.tpl' %}
<<
/Type /FontDescriptor
/FontName /{{ fontName }}
/Ascent {{ ascent }}
/Descent {{ descent }}
/CapHeight {{ capHeight }}
/Flags {{ flags }}
/FontBBox [{% for key, v in fontBBox %}{{ v }} {% endfor %}]
/ItalicAngle {{ italicAngle }}
/StemV {{ stemV }}
/MissingWidth {{ missingWidth }}
/FontFile2 {{ fontFile }}
>>
{% include 'ObjectFooter.tpl' %}