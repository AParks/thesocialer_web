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
                <br/>If you have an idea you're passionate about, <br/>contact <strong>concierge@thesocialer.com</strong> to be a host.
            </div><br/>
            <div id='button-container'>
                <div id='fb-connect' class="fb-login">
                     <div id='left'>
                    <i class="icon-facebook icon-large"></i>
                    </div>
                    <span id='border'></span>
                    <div id='right'>Connect with Facebook</div>
                </div>
            </div>
            </div>
        </div>
        <div id="MainBody">
            
            <div id='popup-what' class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3>Create a popup</h3>
                </div>

                <div class="modal-body">
                    <form action="/A/FeaturedJSON.php" method="post" enctype="multipart/form-data" onSubmit="return validateForm()">

                    <fieldset id='first' class='active'>
                        <input id="headinput" type="text" name="headline" placeholder='Event Headline' />
                        <input id="subheadinput" type="text" name="sub_headline" placeholder='Event Subheadline'/>
                        <textarea name="description" placeholder="Describe what you are going to share and why you think it's awesome"></textarea>
                        <label>
                            <input checked="checked" type="radio" name="is_private" value="false"/>
                            Public
                        </label>
                        <label>
                           <input type="radio" name="is_private" value="true" />
                            Invite Only     
                       </label>
                    </fieldset>
                    <fieldset id='second' class='inactive'>
                         <input class="date datepicker" type="text" name="startDate"/>
                        <input class="date time" type="text" name="startTime" placeholder="HH:MM" />
                          <a id='end_time'>End time?</a><br/>
                        <input class="date datepicker" type="text" name="endDate" />
                        <input class="date time" type="text" name="endTime" placeholder="HH:MM"/>
                        <br/>          
                       <input type="text" id='popup_location' name="location" placeholder="Location" />
                       <div id="map-canvas"></div>
                    </fieldset>
                    <fieldset id='3' class='inactive'>
                           <input id="extrainput" type="text" min="0" name="price" placeholder='Price'/>
<input type="file" name="file[]" id="file" multiple='true' placeholder='Choose images'></input>
                        Spots <input type="number" style="width: 80%; height:100%" id="spots" name="spots" value='0' />
                    </fieldset>
                    <input type="hidden" name="startDate" value="" />
                    <input type="hidden" name="endDate" value="" />
                    <input type="hidden" name="markup" value="" />
                    <input type="hidden" name="action" value="create" />
                    <input type="hidden" name="host" value="{./Viewer/@userId}" />

                </form>
                </div>
                <div class="modal-footer">
                  <a href="#" class="butn">Back</a>
                  <a href="#" class="new_popup_button" id='next'>Next</a>
                </div>
            </div>
            
            
            <a href='/popups/new'>
            <div id='new_popup_button' class='new_popup_button'>
                <i class="icon-edit"></i>
            Create a new popup
            
            </div>
            </a>
            
       

            <ol id="LocationList">
                                                            
                <xsl:for-each select="images/image">

                    <li class="loc">
                        <div class="TitleLocationContainer">
                            <div class="EventTitle">
                                <div><xsl:value-of select="./@headline"/></div>
                                <xsl:choose>
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
                        </xsl:choose>
                               
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
