<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>
  
  <xsl:template match="/Home">
    <div id="MainBody">
      <div class="WelcomeBack">
        Here's what's NOT going on...
	
	<ol id="EventDays">
	  <xsl:for-each select="EventDays/Day">
	    <li>
	      <xsl:attribute name="class">
		<xsl:text>Day</xsl:text>
		<xsl:value-of select="position()" />
		<xsl:if test="@index = 0">
		  <xsl:text> selected</xsl:text>
		</xsl:if>
	      </xsl:attribute>
	      <xsl:attribute name="data-date">
		<xsl:value-of select="." />
	      </xsl:attribute>
	      <xsl:value-of select="@text" />
	    </li>
	  </xsl:for-each>
	</ol>
      </div>
      
      <div class="Home">
        <div class="Events">
          <div class="clear"><![CDATA[]]></div>
          <ul class="EventList">
            <xsl:call-template name="LocationSkeleton" />
          </ul>
        </div>
	<xsl:call-template name="CommentSkeleton" />
        <div class="clear"></div>
      </div>
    </div>
  </xsl:template>

  <xsl:template name="LocationSkeleton">
    <li class="hide LocationSkeleton">
      <div class="Event" data-location-id="">
	<div class="AttendingButtonsHolder">
	  <ul class="AttendingCounts">
	    <li class="AttendingCount"></li>
	  </ul>  
	</div>
	<div class="LocationImage">
	  <div class="TitleLocationContainer">
	    <div class="EventTitle"></div>
	    <div class="EventLocation"></div>
	  </div>
	</div>
      </div>
    </li>
  </xsl:template>

  <xsl:template name="CommentSkeleton">
    <div class="hide CommentSkeleton CommentContainer">
      <a class="UserLink"><img class="UserPhoto" /></a>
      <div class="NameContainer">
        <a class="UserLink"><span class="UserName"></span></a>
      </div>
      <div class="Comment"></div>
    </div>
  </xsl:template>
</xsl:stylesheet>
