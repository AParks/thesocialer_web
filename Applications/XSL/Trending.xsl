<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>
  
  <xsl:template match="/Home">
    <div id="MainBody">
   <!--   <div id="Top">
	<xsl:if test="FeaturedEvent">
	  <h2>Spotlighted Event</h2>
	  <div id="FeaturedEvent">
	    <xsl:attribute name="data-event-id">
	      <xsl:value-of select="FeaturedEvent/@featuredEventId" />
	    </xsl:attribute>	    
	    <div id="FeaturedEvent_Markup">
	      <xsl:value-of select="FeaturedEvent/@markup" disable-output-escaping="yes" />
	    </div>
	    <div class="FeaturedAttendingList">

	      <ul class="AttendingCounts">
		<li class="MaybeCount" data-status="maybe" active="{count(FeaturedEventAttendanceManager/attendanceStatuses/maybe/Member[@userId = /Home/Viewer/Member/@userId ])}">
		  <strong>
		    <xsl:value-of select="count(FeaturedEventAttendanceManager/attendanceStatuses/maybe/Member)" />
		  </strong>
		  Maybe
		</li>
		<li class="AttendingCount" data-status="yes" active="{count(FeaturedEventAttendanceManager/attendanceStatuses/yes/Member[@userId = /Home/Viewer/Member/@userId ])}">
		  <strong>
		    <xsl:value-of select="count(FeaturedEventAttendanceManager/attendanceStatuses/yes/Member)" />
		    </strong> Going
		</li>
	      </ul>

	    </div>
	  </div>
	</xsl:if>
      </div> -->
      <div class="WelcomeBack">	
        Here's what's going on in Philadelphia... 

	
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
	<!--<div class="AttendingButtonsHolder">
	  <ul class="AttendingCounts">
	    <li class="AttendingCount"></li>
	  </ul>  
	</div> -->
        <a id='LocationImageLink' href=''>
	<div class="LocationImage">
	  <div class="TitleLocationContainer">
	    <div class="EventTitle"></div>
	    <div class="EventLocation"></div>
	  </div>
	</div>
        </a>
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
