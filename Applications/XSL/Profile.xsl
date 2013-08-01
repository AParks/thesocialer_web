<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/Profile">
        <div id="MainBody">
            <xsl:choose>
                <xsl:when test="./Viewer/Member/@userId = ./Member/@userId">
                    <xsl:attribute name="class">OwnProfile</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <div style="float:right;color:#999;">
                        <xsl:choose>
                            <xsl:when test="Friends/@friendsWithViewer = '1'">
                                <xsl:text>You and </xsl:text>
                                <xsl:value-of select="Member/@firstName" />
                                <xsl:text> are friends</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                                <a href="#" id="FriendRequest">
                                    <xsl:text>Get Social with </xsl:text>
                                    <xsl:value-of select="Member/@firstName" />
                                </a>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:text> | </xsl:text>
                        <a href="#" id="SendMessage">
                            <xsl:text>Send Message</xsl:text>
                        </a>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
          
            <div id="LeftColumn">
                <xsl:call-template name="LeftColumn" />
            </div>
            <div id="RightColumn">
                <xsl:call-template name="RightColumn" />
            </div>
            <div class="clear"><![CDATA[]]></div>
        </div>
    </xsl:template>

    <xsl:template name="LeftColumn">
        <div class='ProfileBox'>
             <div class='heading'>
                 <xsl:if test="./Viewer/Member/@userId = Member/@userId">
                     <span id='edit'>
                         <i class="icon-edit icon-large"></i>
                         <i style='display: none' class="icon-save icon-large"></i>
                     </span> 
                 </xsl:if>
                 <div class='ProfileTitle'>
                     <xsl:value-of select="Member/@firstName" />
                     <xsl:text> </xsl:text>
                     <xsl:value-of select="Member/@lastName" />
                 </div>

                     <textarea style='display: none' data-field='FirstName' class="ProfileTitle"  readonly='true'>
                         <xsl:value-of select="Member/@firstName" />
                     </textarea>
                     <textarea style='display: none'  data-field='LastName' class="ProfileTitle"  readonly='true'>
                         <xsl:value-of select="Member/@lastName" />
                     </textarea>
                     <textarea class='loc' data-field='Location' readonly='true'>
                         <xsl:value-of select="Member/@Location" />
                     </textarea>
             </div>
        <xsl:choose>
            
            <xsl:when test="./Member/@fb_id and count(photos/Photo[@isDefault='1']) = 0 ">
                <img src="https://graph.facebook.com/{./Member/@fb_id}/picture?type=large" class='Photo'/>
            </xsl:when>
            <xsl:otherwise>
                <soc:photo class="Photo">
                    <xsl:attribute name="userId">
                        <xsl:value-of select="Member/@userId" />
                    </xsl:attribute> 
                </soc:photo>
            </xsl:otherwise>
        </xsl:choose>
        
        

        </div>

        
        <xsl:if test="count(photos/Photo) = 0 and Member/@fb_id = '' ">  
            <xsl:if test="./Viewer/Member/@userId = Member/@userId">
                <a href="#" id="UploadPhotoLink">Add Profile Photo</a>
            </xsl:if>
        </xsl:if>
        <div class="ProfileBox">
            <div class='heading'>
                <xsl:choose>
                    <xsl:when test="./Viewer/Member/@userId = Member/@userId">
                        <xsl:text>Your</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="Member/@firstName" />
                        <xsl:text>'s</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text> Connections</xsl:text>
            </div>
            <div class="FriendContainer">
                <xsl:for-each select="Friends/friend">
                    <soc:photo size="Small" link="true">
                        <xsl:attribute name="class">
                            <xsl:text>Friend</xsl:text>
                            <xsl:if test="position( ) mod 4 = 1"> first</xsl:if>
                        </xsl:attribute>
                        <xsl:attribute name="userId">
                            <xsl:value-of select="." />
                        </xsl:attribute>
                    </soc:photo>
                </xsl:for-each>
            </div>
            <xsl:if test="./Viewer/Member/@userId = Member/@userId">
                <div class="LinkContainer">
                    <a href="/search">Meet New People! Start Your Search.</a>
                </div>
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template name="RightColumn">
       <!--  <div class="ProfileStats"> 
            <span class='plus first'>
                <i class="icon-star"></i> 25 reviews
            </span>
            <xsl:choose>
            <xsl:when test='Member/@fb_id'>
            <span class='plus first' id='fb'>
                <i class="icon-facebook"></i>
                <span> friends</span>
            </span>
            <span class='plus last'>
                <i class="icon-envelope"></i> Email verified
            </span>
            </xsl:when>
            <xsl:otherwise>
                <span class='plus first last'>
                    <i class="icon-envelope"></i> Email verified
                </span>
            </xsl:otherwise>
            </xsl:choose>
            <<span class='plus'>
                <i class="icon-heart"></i> 20 likes
            </span> </div> -->
        
               <div class="ProfileBox">
                    <div class='heading'>
                        <xsl:text>About Me</xsl:text>
                        <xsl:if test="./Viewer/Member/@userId = Member/@userId">
                            <span id='edit'>
                                <i class="icon-edit icon-large"></i>
                                <i style='display: none' class="icon-save icon-large"></i>
                            </span>
                        </xsl:if>
                    </div>
                    <xsl:if test="Member/@College != '' ">
                        <strong>School: </strong> 
                        <xsl:value-of select="Member/@College" />
                    </xsl:if>   
                   <textarea id='aboutme' readonly='true' data-field='AboutMe'>
                       
                       <xsl:value-of select="Member/@AboutMe" />
                   </textarea>

                <div class="clear"></div>
                </div>
        <div class="ProfileBox hide">
            <div class='heading'>
                <xsl:text>Places </xsl:text>
                <xsl:choose>
                    <xsl:when test="./Viewer/Member/@userId = Member/@userId">
                        <xsl:text>You Follow</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="Member/@firstName" />
                        <xsl:text> Follows</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
            <div id="LocationNamePopup" class="hide"></div>
            <ol id="LikedLocations">
                <xsl:call-template name="LikedLocationSkeleton" />
            </ol>
            <div class="clear"></div>
        </div>

        <div class="ProfileBox hide">
            <div class='heading'>
                <xsl:text> Popups Attended</xsl:text>
            </div>
            <ol id="PastEventsAttended">
                <xsl:call-template name="PastEventSkeleton" />
            </ol>
            
            <div class="clear"></div>
        </div>
         <div class="ProfileBox hide">
            <div class='heading'>
                <xsl:text>PopUps Hosted</xsl:text>
            </div>
            <ol id="PastEventsHosted">
                <xsl:call-template name="PastEventSkeleton" />
            </ol>
            
            <div class="clear"></div>
        </div>

        <xsl:choose>
            <xsl:when test="count(photos/Photo) > 1">
                <div class="ProfileBox">
                      <div class='heading'>Photos</div>
                    <xsl:call-template name="Photos" />
                </div>
            </xsl:when>
            <xsl:when test="./Viewer/Member/@userId = Member/@userId">
                <a href="#" id="UploadPhotoLink">Add Photos</a> 
            </xsl:when>
        </xsl:choose>


    </xsl:template>

    <xsl:template name="ProfileField">
        <xsl:param name="field" />
        <xsl:param name="displayField" />
        <xsl:param name="value" />

        <div>
            <xsl:attribute name="class">
                <xsl:text>element </xsl:text>
                <xsl:value-of select="$field" />
            </xsl:attribute>
            <label>
                <xsl:value-of select="$displayField" />
                <xsl:text>:</xsl:text>
            </label>
            <xsl:choose>
                <xsl:when test="./Viewer/Member/@userId = ./Member/@userId">
                    <div class="value editable">
                        <input>
                            <xsl:attribute name="data-field">
                                <xsl:value-of select="$field" />
                            </xsl:attribute>
                            <xsl:attribute name="value">
                                <xsl:value-of select="$value" /> 
                            </xsl:attribute>
                        </input>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="value">
                        <xsl:attribute name="data-field">
                            <xsl:value-of select="$field" />
                        </xsl:attribute>
                        <xsl:value-of select="$value" /> 
                    </div>
                </xsl:otherwise>
            </xsl:choose>
            <div class="clear"></div>
        </div> 
    </xsl:template>

    <xsl:template name="PastEventSkeleton">
        <li id="PastEventSkeleton">
            <a />
            <br />
            <div class="PastEventInfo">
            </div>
            <button class="standard Gray">See Who Attended</button>
        </li>
    </xsl:template>
  
    <xsl:template name="LikedLocationSkeleton">
        <li id="LikedLocationSkeleton">
            <a>
                <img />
            </a>
        </li>
    </xsl:template>

    <xsl:template name="Photos">
        <div class="Photos">
            <div class="PhotoThumbs">
                <xsl:for-each select="photos/Photo[not(@path='facebook') and @isDefault='']">
                    <img>
                        <xsl:attribute name="data-large">
                            <xsl:value-of select="paths/@Large" />
                        </xsl:attribute>
                        <xsl:attribute name="photo-id">
                            <xsl:value-of select="./@photoId" />
                        </xsl:attribute>
                        <xsl:if test="position() mod 7 = 0">
                            <xsl:attribute name="class">last</xsl:attribute>
                        </xsl:if>
                        <xsl:attribute name="src">
                            <xsl:value-of select="paths/@Medium" />
                        </xsl:attribute>
                    </img>
                </xsl:for-each>
                
                <xsl:for-each select="photos/Photo[@isDefault='' and @path='facebook']">
                    <img width='80' height='80' src="https://graph.facebook.com/{/Profile/Viewer/Member/@fb_id}/picture?type=square"
                    data-large='https://graph.facebook.com/{/Profile/Viewer/Member/@fb_id}/picture?type=large'>
                        <xsl:attribute name="photo-id">
                            <xsl:value-of select="./@photoId" />
                        </xsl:attribute>
                        <xsl:if test="position() mod 7 = 0">
                            <xsl:attribute name="class">last</xsl:attribute>
                        </xsl:if>
                    </img>
                </xsl:for-each>
            </div>

            <div class="PhotoLinks">
                <xsl:if test="./Viewer/Member/@userId = Member/@userId">
	                              <a href="#" id="UploadPhotoLink">Add Photos</a> 

                    <!--<xsl:choose>
                        <xsl:when test="count(photos/Photo) = 0">
	      
                           <a href="#" id="UploadPhotoLink">Add Profile Photo</a> 
                        </xsl:when>
                        <xsl:otherwise>
                        </xsl:otherwise>
                    </xsl:choose> -->
                    <xsl:if test="photos/@hasMore='1'">
                        <xsl:text> | </xsl:text>
                    </xsl:if>
                </xsl:if>

                <xsl:if test="photos/@hasMore='1'">
                    <a>
                        <xsl:attribute name="href">
                            <xsl:text>/photos/</xsl:text>
                            <xsl:value-of select="/Profile/Member/@userId" />
                        </xsl:attribute>
                        <xsl:text>View All</xsl:text>
                    </a>
                </xsl:if>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
