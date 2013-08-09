<?xml version="1.0"?>
<!--
    Document   : Community.xsl
    Created on : August 2, 2013, 4:16 PM
    Author     : anna parks
    Description:
        Purpose of transformation follows.
-->


<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    
    <xsl:template match="/Community">
        <div id="MainBody">
            <xsl:if test="./Viewer/@userId = 193">
                <button id='image-upload'>Choose Images</button>
            </xsl:if>
                <xsl:if test='count(Photos/Photo) > 0'>
                    <h2>Here are some photos from past events...</h2>
                </xsl:if>    
                <div id='photos'>
                <xsl:apply-templates select="Photos/Photo" />
            </div>
            <div class="modal-photo fade">
                <div class="modal-dialog">
                    <div class="modal-content">
      
                        <div class="modal-body">
                            <img/>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->        
        </div>
    </xsl:template>


    <xsl:template match="Photo">
        <div class='popup_photo UserPhoto' url='{./@path}' data-large='{./@path}'>
                <img src='{./@path}' />
        </div>
       
    </xsl:template>
</xsl:stylesheet>
