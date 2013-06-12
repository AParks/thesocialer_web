<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:soc="http://kemmerer.co">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/Network">
    <div id="MainBody">
      <div class="FriendsMain">
        <h1>Your Social Network</h1>
        <table id="FriendList">
        
        <xsl:choose>
            <xsl:when test="count(Friends/Friends/Member) > 0"> 
              <xsl:apply-templates select="Friends/Friends/Member" />
            </xsl:when>
            <xsl:otherwise>
              <td class="NoConnections">No connections.</td>
            </xsl:otherwise>
         </xsl:choose>
          
        </table>
        <div class="UserSuggestions">
       <!--   <h2>Members You Might Be Interested In</h2> -->
          <ul class="UserRecommendationList">
            <xsl:call-template name="UserRecommendationSkeleton" />
          </ul>
	<!--  <div class="SeeMoreButton">See More Users</div> -->
        </div>
        <div class="clear"></div>
      </div>
    </div>

  </xsl:template>

  <xsl:template match="Member">
    <xsl:if test="position( ) mod 2 = 1">
      <xsl:text disable-output-escaping="yes">&lt;tr></xsl:text>
    </xsl:if>
    <td>
      <xsl:attribute name="class">
        <xsl:text>Friend</xsl:text>
        <xsl:if test="position( ) mod 2 = 1">
          <xsl:text> first</xsl:text>
        </xsl:if>
      </xsl:attribute>
      <soc:photo class="Photo" size="Medium" link="true">
        <xsl:attribute name="userId"><xsl:value-of select="@userId" /></xsl:attribute>
      </soc:photo>
      <div class="UserInfo">
        <a>
          <xsl:attribute name="href">
            <xsl:text>/profile/</xsl:text>
            <xsl:value-of select="@userId" />
          </xsl:attribute>
          <strong><xsl:value-of select="@firstName" /><xsl:text> </xsl:text><xsl:value-of select="@lastName" /></strong>
        </a>
        <xsl:text>, </xsl:text>
        <xsl:value-of select="@age" /><br />
        <xsl:value-of select="@Location" /><br />
        <xsl:value-of select="@College" /><br />
        <!--<xsl:if test="@AboutMe != ''">
          <span class="AboutMe"><xsl:value-of select="@AboutMe" /></span><br />
        </xsl:if>-->
      </div>
      <div class="clear"></div>
    </td>
    <xsl:if test="position( ) mod 2 = 0 or position( ) = last( )">
      <xsl:if test="position( ) mod 2 = 1 and position( ) = last( )">
        <td class="empty"></td>
      </xsl:if>
      <xsl:text disable-output-escaping="yes">&lt;/tr></xsl:text>
    </xsl:if>
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
