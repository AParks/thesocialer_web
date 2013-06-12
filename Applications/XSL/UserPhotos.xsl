<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/UserPhotos">
    <div id="MainBody">
      <div class="UserPhotos">
        <h2>
          <xsl:choose>
            <xsl:when test="./Viewer/Member/@userId = ./Member/@userId">
              <xsl:text>Your Photos</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="./Member/@firstName" />
              <xsl:text> </xsl:text>
              <xsl:value-of select="./Member/@lastName" />
              <xsl:text>'s photos</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </h2>

        <xsl:apply-templates select="Photos/Photo" />
      </div>
    </div>
  </xsl:template>

  <xsl:template match="Photo">
    <img class="UserPhoto">
      <xsl:attribute name="src">
        <xsl:value-of select="paths/@Medium" />
      </xsl:attribute>
      <xsl:attribute name="data-large">
        <xsl:value-of select="paths/@Large" />
      </xsl:attribute>
    </img>
  </xsl:template>
</xsl:stylesheet>
