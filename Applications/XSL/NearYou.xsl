<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/Locations">
    <div id="MainBody">
      <div class="LocationsMain">
        
        
        <div class="LeftSide">
          <xsl:call-template name="LeftSide" />
        </div>
	<!--<div class="RightSide">
          <xsl:call-template name="RightSide" />
        </div>-->
	<xsl:call-template name="Bottom" />
        <div class="clear"></div>
      </div>
    </div>
  </xsl:template>

  <xsl:template name="RightSide">
    <div id="Map"></div>
    <!--<div class="PastEventsAttendedContainer">
      <h2>Places You've Attended</h2>
      <ol id="PastEventsAttended">
        <xsl:call-template name="PastEventSkeleton" />
      </ol>
    </div>-->
  </xsl:template>

  <xsl:template name="LeftSide">
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
            <li class="MaybeCount">
            	<strong>
            	<xsl:value-of select="count(FeaturedEventAttendanceManager/attendanceStatuses/maybe/Member)" />
            	</strong>
             	Maybe
             </li>
             <li class="AttendingCount">
            	<strong>
            	<xsl:value-of select="count(FeaturedEventAttendanceManager/attendanceStatuses/yes/Member)" />
            	</strong> Going
            </li>
          </ul>
        </div>
      </div>
    </xsl:if>
  </xsl:template>
  <xsl:template name="Bottom">
    <h2>Explore your interests:</h2> (pick as many as you like)
    <div id="tagfilter">
    	
    	<div class="tagbutton first" data-tag-desc="nightlife">nightlife</div>
    	<div class="tagbutton" data-tag-desc="great beer">great beer</div>
    	<div class="tagbutton" data-tag-desc="nightclub">nightclub</div>
    	<div class="tagbutton" data-tag-desc="rooftop">rooftop</div>
    	<div class="tagbutton" data-tag-desc="dancing">dancing</div>
    	<div class="tagbutton" data-tag-desc="live music">live music</div>
    	<div class="tagbutton" data-tag-desc="sports">sports</div>
    	<div class="tagbutton" data-tag-desc="fun">fun</div>
    	<div class="tagbutton" data-tag-desc="arts">arts</div>
    	<div class="tagbutton" data-tag-desc="museums">museums</div>
    	<div class="tagbutton" data-tag-desc="nature">nature</div>    	
    	<div class="tagbutton" data-tag-desc="active">active</div>
    	<div class="tagbutton" data-tag-desc="wine">wine</div>
    	<div class="tagbutton" data-tag-desc="local hangout">local hangout</div>
    	<div class="tagbutton" data-tag-desc="cocktails">cocktails</div>
    	<div class="tagbutton last" data-tag-desc="outdoors">outdoors</div>
    </div>
    
    
    <ol id="LocationList">
    </ol>
    <div class="clear"></div>
    <ol id="LocationListPaging"></ol>
  </xsl:template>

  <xsl:template name="PastEventSkeleton">
    <li id="PastEventSkeleton">
      <a />
      <br />
      <div class="PastEventInfo">
      </div>
    </li>
  </xsl:template>
</xsl:stylesheet>
