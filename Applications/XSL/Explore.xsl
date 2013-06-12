<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/Locations">
    <div id="MainBody">
      <div class="LocationsMain">
	<xsl:call-template name="Bottom" />
        <div class="clear"></div>
      </div>
    </div>
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
    <ol id="LocationList"></ol>
    <div class="clear"></div>
    <ol id="LocationListPaging"></ol>
  </xsl:template>

</xsl:stylesheet>
