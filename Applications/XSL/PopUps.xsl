<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : PopUps.xsl
    Created on : May 29, 2013, 1:34 PM
    Author     : aparks
    Description:
        Purpose of transformation follows.
-->
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/Popups">
        <div id='img'> 
            <div id='popup-desc-start'>Socialer Popups are ... </div>
            <div id='popup-desc'>
                unique events hosted by and 
                <br/>
                for members of our
                <br/> community. 
            </div>
        </div>
        <div id="MainBody">
            
            <ol id="LocationList">
                <xsl:for-each select="images/image">
                    <li class="loc">
                        <div class="TitleLocationContainer">
                            <div class="EventTitle">
                                <div><xsl:value-of select="./@headline"/></div>
                                <a href='#' data-placement='top' data-toggle="tooltip" title="Bookmark it" class="EventPrice">
                                    <i class="icon-bookmark icon-large"></i>

                                </a>
                            </div>
                        </div>

                        <a href="/location/featured/{./@id}">
                            <img src="{.}"></img>
                            
                        </a>
                        
                        <div class="Host">
                            <xsl:apply-templates select="./Host/Member" />
                            
                            <xsl:choose>
                                <xsl:when test='count(./FeaturedEventAttendanceManager/attendanceStatuses/Member) > 7 '>
                                 
                                    <div class='spots'>
                                        <i class="icon-user icon-large"></i>
                                        <span><br/><xsl:value-of select="./@spots_purchased"/>
                                        going</span>
                                    </div>
                                    <div class='attendees attendees_more'>
                                        <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/Member" />
                                    </div>
                                </xsl:when>
                                <xsl:when test='./@spots_purchased != 0 '>
                                   
                                    <div class='spots'>
                                    <i class="icon-user icon-large"></i>
                                    <span><br/><xsl:value-of select="./@spots_purchased"/>
                                    going</span>
                                    </div>
                                    <div class='attendees'> 
                                        <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/Member" />
                                    </div>
                                </xsl:when>
                            </xsl:choose>
                            
                            </div>
                    </li>
                
                </xsl:for-each>
            </ol>
        </div>

    </xsl:template>

    <xsl:template match="Host/Member">
        
                           
        <a id='host' href="/profile/{../../@host}" style="background-image: url('/photo/{../../@host}/Small')">
            <!--<a data-user-id="{../../@host}" href="/profile/{../../@host}">
                <img width='44px' height='44px' src="/photo/{../../@host}/Small"  />
               
            </a> -->
            <xsl:value-of select="./@firstName"/>
        </a>
        <div id='info'>
            
            <div>
                <i class="icon-calendar icon-large"></i>
                <br/>
                <xsl:value-of select="../../DateObject/@shortMonth"/>
                <span>. </span>
                <xsl:value-of select="../../DateObject/@date"/>
            </div>
            <div>
                <i class="icon-time icon-large"></i>
                <br/>
                <xsl:value-of select="../../DateObject/@time"/>
            </div>
            <div>
                <i class="icon-usd icon-large"></i>
                <br/>
                <xsl:value-of select="../../@price"/>
            </div>
            <div>
                <i class="icon-user icon-large"></i>
                <br/>
                <xsl:value-of select="../../@spots"/>
                <span> spots</span> 
            </div>
        </div>
                
    </xsl:template>


    <xsl:template match="Member">
        <xsl:if test="../../../@host != ./@userId">
            <xsl:choose>
                <xsl:when test="/FeaturedEventViewer/FeaturedEventAttendanceManager/attendanceStatuses/Member/@userId = /FeaturedEventViewer/Viewer/@userId">
                    <a data-user-id="{./@userId}" href="/profile/{./@userId}">
                        <img src="/photo/{./@userId}/Small" class="UserPhoto" />
                    </a>
                </xsl:when>
                <xsl:otherwise>
                    <a data-user-id="{./@userId}">
                        <img src="/photo/{./@userId}/Small" class="UserPhoto" />
                    </a>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>
    </xsl:template>


</xsl:stylesheet>
