<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:soc="http://kemmerer.co">
  <xsl:output method="html" omit-xml-declaration="yes" />

  <xsl:template match="/Thread">
    <div id="MainBody">
      <div class="Messages">
        <h1>Messages</h1>
        <ol id="Messages">
	  <li class="RespondBox">
            <textarea id="Response">
              <xsl:attribute name="recipient">
                <xsl:value-of select="@threadWith" />
              </xsl:attribute>
            </textarea>
            <br />
            <button id="RespondButton" class="standard Gray">Send</button>
          </li>
          <xsl:apply-templates select="Thread/messages" />
        </ol>
      </div>
    </div>
  </xsl:template>

  <xsl:template match="Message">
    <li>
      <soc:photo size="Small">
        <xsl:attribute name="userId">
          <xsl:value-of select="Member/@userId" />
        </xsl:attribute>
      </soc:photo>
      <div class="Timestamp">
        <xsl:value-of select="@sentAtFormatted" />
      </div>
      <div class="Content">
        <div class="SenderName">
          <xsl:value-of select="Member/@firstName" />
          <xsl:text> </xsl:text>
          <xsl:value-of select="Member/@lastName" />
        </div>
        <div class="MessageBody">
          <xsl:value-of select="@message" disable-output-escaping="yes"/>
        </div>
      </div>
      <div class="clear"></div>
    </li>
  </xsl:template>
</xsl:stylesheet>
