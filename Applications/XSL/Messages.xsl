<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:soc="http://kemmerer.co">
  <xsl:output method="html" omit-xml-declaration="yes" />

  <xsl:template match="/Messages">
    <div id="MainBody">
      <div class="Messages">
        <h1>Messages</h1>
        <xsl:choose>
            <xsl:when test="count(Messages/messages) > 0"> 
              <ol id="Messages">
		<xsl:apply-templates select="Messages/messages" />
	      </ol>
            </xsl:when>
            <xsl:otherwise>
              <div class="NoMessages">No messages.</div>
            </xsl:otherwise>
         </xsl:choose>
        
      </div>
    </div>
  </xsl:template>

  <xsl:template match="Message">
    <li>
      <xsl:if test="not(@readAt)">
        <xsl:attribute name="class">
          <xsl:text>new</xsl:text>
        </xsl:attribute>
      </xsl:if>
      <xsl:attribute name="thread-id">
        <xsl:value-of select="Member/@userId" />
      </xsl:attribute>
      <soc:photo size="Small">
        <xsl:attribute name="userId">
          <xsl:value-of select="Member/@userId" />
        </xsl:attribute>
      </soc:photo>
      <div class="Timestamp">
        <xsl:value-of select="@sentAtFormatted" />
      </div>
      <div>
        <div class="SenderName">
          <xsl:value-of select="Member/@firstName" />
          <xsl:text> </xsl:text>
          <xsl:value-of select="Member/@lastName" />
        </div>
        <div class="Preview">
          <xsl:value-of select="@preview" />
        </div>
      </div>
      <div class="clear"></div>
    </li>
  </xsl:template>
</xsl:stylesheet>
