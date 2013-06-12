<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/Search">
    <div id="MainBody">
      <div class="SearchMain">
        <div id="Results">
          <h2>Search</h2>
          <div class="searchtypes">
          	<span class="searchtype active" id="people">people</span> | <span class="searchtype" id="places">places</span>
          </div>
	  <div class="DateInfo hide">
	    <span class="DateText"></span>
	    <span class="ChangeDateButton"> +1 day</span>
	  </div>
          <form id="SearchForm">
            <input id="SearchField" type="text" value="Search by name, email, or college" class="default" />
            <button class="Blue standard" id="SearchButton">Search</button>
          </form>
        </div>


    <!--    <div class="UserSuggestions">
          <h2>Suggested Connections</h2>
          <ul class="UserRecommendation_List">
            <xsl:call-template name="UserRecommendationSkeleton" />
          </ul>
	  <div class="SeeMoreButton">See More Users</div>
        </div> -->
        <div class="clear"></div>
      </div>
    </div>
  </xsl:template>

  <xsl:template name="UserRecommendationSkeleton">
    <li class="hide UserRecommendationSkeleton UserRecommendation">
      <a class="UserLink"><img class="UserPhoto" /></a>
      <div class="RightSide">
        <div class="NameLocationContainer">
          <a class="UserLink"><span class="UserName"></span></a>
          <span class="UserLocation"></span>
        </div>
        <ul class="UserDetails">
          <li>College: <span class="UserCollege" /></li>
          <li>Age: <span class="UserAge" /></li>
        </ul>
        <button class="Gray standard GetSocialButton">Get Social</button>
      </div>
      <div class="clear"></div>
    </li>
  </xsl:template>
</xsl:stylesheet>
