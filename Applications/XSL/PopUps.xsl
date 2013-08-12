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
            
            <div id='container-black'></div>
            <div id='container'>
            <div id='popup-desc-start'>Explore outside your circle.</div><br/>
            <div id='popup-desc'>
                Host and discover unique and engaging events, <br/> while meeting new, interesting people.
              <!--  <br/>If you have an idea you're passionate about, <br/>contact <strong>concierge@thesocialer.com</strong> to be a host.-->
                
            </div>
            <xsl:choose>
            <xsl:when test='./Viewer/@userId = -1'>
                    <br/>
                <div id='button-container'>
                    <div id='fb-connect' class="fb-login">
                         <div id='left'>
                        <i class="icon-facebook icon-large"></i>
                        </div>
                        <span id='border'></span>
                        <div id='right'>Connect with Facebook</div>
                    </div>
                </div>
                <div id='popup-desc'>
                    or
                </div>
                <div id='button-container'>
                    <div id='join-email' class='new_popup_button'>
                         Join with Email
                    </div>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <br/>
                 <a href='/popups/new'>
            <div id='new_popup_button' class='new_popup_button'>
                <i class="icon-edit"></i>
                Create a new popup</div>
                 </a> <br/>
              <!--    <div id='popup-safety'>
                     <div id='verified'>
                         <i class="icon-ok-sign icon-large"></i>
                     </div>
                     Safety guaranteed. All hosts and events are personally verified by the Socialer team.
                   <br/>
                         <i class="icon-envelope  icon-large"></i>
                     
                     Get to know each other before the event via our messaging system
                     <br/>
                         <i class="icon-lock  icon-large"></i>
                     Pay and get paid via Stripe, a secure payments system.
                 </div>-->
            </xsl:otherwise>
            </xsl:choose>
            </div>
        </div>
        <div id="MainBody">
       

            <ol id="LocationList">
                                                            
                <xsl:for-each select="images/image">

                    <li class="loc">
                        <div class="TitleContainer">
                            <div class="EventTitle">
                                <div id='title'><xsl:value-of select="./@headline"/></div>
                             <!--    <div title="Verified" id='verified'>
                                    <i class="icon-ok-sign icon-large"></i>
                                </div> -->
                           <!--    <xsl:choose>
                            <xsl:when test='../../Viewer/@userId != -1'>
                                 <a data-placement='top' data-toggle="tooltip" title="Bookmark it" class="logged_in EventPrice">
                                    <i class="icon-bookmark icon-large"></i>
                                </a>
                            </xsl:when>
                            <xsl:otherwise>
                                <a data-placement='top' data-toggle="tooltip" title="Bookmark it" class="notLoggedIn EventPrice">
                                    <i class="icon-bookmark icon-large"></i>
                                </a>
                            </xsl:otherwise>
                        </xsl:choose>-->
                                <xsl:if test='../../Viewer/@userId = ./@host'>
                                 <a title="Cancel Event" class="EventPrice" data-event-id='{./@id}'>
                                    <i class="icon-trash icon-large"></i>
                                </a>
                            </xsl:if>
                            </div>
                        </div>
                        <xsl:choose>
                            <xsl:when test='../../Viewer/@userId != -1'>
                                <a href="/location/featured/{./@id}">
                                    <img src="{.}"></img>
                                </a>
                            </xsl:when>
                            <xsl:otherwise>
                                <a class='notLoggedIn'>
                                    <img src="{.}"></img>
                                </a>
                            </xsl:otherwise>
                        </xsl:choose>
                        
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
                <xsl:choose>
                    <xsl:when test="../../@price = 0">
                        free
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="../../@price"/>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
            <div>
                <i class="icon-user icon-large"></i>
                <br/>
                <xsl:choose>
                    <xsl:when test='../../@spots &lt; 0'>
                    &#8734;
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="../../@spots"/>
                    </xsl:otherwise>
                </xsl:choose>
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
