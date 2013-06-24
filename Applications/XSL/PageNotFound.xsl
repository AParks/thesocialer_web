<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : PageNotFound.xsl
    Created on : June 24, 2013, 4:23 PM
    Author     : root
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/PageNotFound">
        <div id="MainBody">
            <div style="padding: 20px;">
                Page Not Found. 
            </div>
        </div>        
    </xsl:template>

</xsl:stylesheet>
