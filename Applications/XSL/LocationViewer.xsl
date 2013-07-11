<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
  <xsl:output method="html" omit-xml-declaration="yes" />

  <xsl:template match="/LocationViewer">
    <div class="LocationViewer" id="MainBody">

      <div id="leftcontainer">
	<div class="LocationImage">
	  <div class="LocationDetails" >
	    <h1><xsl:value-of select="./Location/@name" /></h1>
	<!--    <div class="LocationRight">
	      <div class="DateContainer">
		<div class="DateContainer_Day">
		  <xsl:value-of select="./DateObject/@shortDay" />
		</div>
		<div class="DateContainer_Date">
		  <xsl:value-of select="./DateObject/@shortMonth" />
		  <br />
		  <xsl:value-of select="./DateObject/@date" />
		</div>
	      </div>
	    
	      <xsl:if test="./Viewer/@userId != -1">
		<div class="AttendingButtons_Holder">
		  <div class="AttendingButtons">
		    <div data-Status="yes" class="Button AttendingButton_Yes">Going</div>
		    <div style="display:none;" data-Status="maybe" class="Button AttendingButton_Maybe">Maybe</div>
		    <div style="display:none;" data-Status="no" class="Button AttendingButton_No">No</div>
		  </div>
		</div>
	      </xsl:if>
	    </div> -->
	    
	    
	    
	  </div>
	  <img src="/Photos/Locations/{./Location/@image}" />
	</div>

	<!-- <div class="Attendees">
	  <div class="AttendeesYes">
	    <strong>
	      <span class="LocationAttendingCount"><xsl:value-of select="count(./AttendanceManager/attendanceStatuses/yes/Member)" /></span> Going
	    </strong>
	    <xsl:apply-templates select="./AttendanceManager/attendanceStatuses/yes/Member" />
	  </div>
	</div> -->
	
	<div class="LocationComments">
	  <h3>Upcoming Events</h3>
       <!--    This event was favorited
          <strong>
               <span class="LocationAttendingCount">
                  <xsl:value-of select="count(./AttendanceManager/attendanceStatuses/yes/Member)" />
              </span> 
          </strong>
          <span class='time'>
              <xsl:choose>
                <xsl:when test="count(./AttendanceManager/attendanceStatuses/yes/Member) = 1">
                    time
                </xsl:when>
                <xsl:otherwise>
                    times
                </xsl:otherwise>
            </xsl:choose> 
          </span> -->

            
	  <hr />

	  <div class="LocationCommentList">
	    <xsl:call-template name="LocationCommentSkeleton" />
	  </div>

             
          <xsl:if test="./Viewer/@userId != -1">
              <div>
                  <img class="MyPhoto" src="/photo/{./Viewer/@userId}/Small" />
                  <textarea class="NewComment" placeholder="Suggest an event"></textarea>
              </div>
	  </xsl:if>
	</div>
        
        <h3>Comments</h3>
	  <hr />
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    var disqus_shortname = 'thesocialer';

                    /* * * DON'T EDIT BELOW THIS LINE * * */
                    (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
                <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

      </div>	

      <div id="rightcontainer">	
	<div class="LocationInfoContainer">
            <br/>
            <xsl:choose>
	      <xsl:when test='./Location/@userLikes = 1'>
                  <div class="LocationLikeButton">
                      Following
                  </div>
	      </xsl:when>
              <xsl:when test="./Viewer/@userId = -1">
              <div>
                   <div class="LocationLikeNotLoggedIn"> 
                      <span id='plus'>+</span>   
                      <span id='follow'>Follow</span>
                  </div>
              </div>
              </xsl:when>
	      <xsl:otherwise>
                  <div class="LocationLikeButton"> 
                      <span id='plus'>+</span>   
                      <span id='follow'>Follow</span>
                  </div>
	      </xsl:otherwise>
	    </xsl:choose>
	  <h1>About The Venue:</h1>
	  <h5>Popularity</h5>
	  <p>
	    <span class="LocationLikeCount"><xsl:value-of select="./Location/@likes" /></span> followers
	    
	  </p>
	  <h5>Website:</h5>
	  <p>
	    <a target="_blank">
	      <xsl:attribute name="href">
		<xsl:value-of select="./Location/@website" />
	      </xsl:attribute>
	      <xsl:value-of select="./Location/@website" />
	    </a>
	  </p>
	  <h5>Description:</h5>
	  <p><xsl:value-of select="./Location/@description" disable-output-escaping="yes" /></p>
	  <h5>Address:</h5>
	  <p><xsl:value-of select="./Location/@streetAddress" /></p>
	</div>
	<div id="LocationMap"></div>
      </div>
      <div class="clear"></div>
    </div>

</xsl:template>

<xsl:template match="Member">
  <a data-user-id="{./@userId}" href="/profile/{./@userId}">
    <img src="/photo/{./@userId}/Small" class="UserPhoto" />
  </a>
</xsl:template>


<xsl:template name="LocationCommentSkeleton">
 <!-- <li class="hide LocationCommentSkeleton LocationCommentContainer"> -->
 
 <div class=" LocationCommentSkeleton LocationCommentContainer">

      <div class="eventContainer">
       <!-- <div class="DeleteComment">✖</div> -->
       <table>
               <tr>
                   <td>
                       <div class="DateContainer">
                           <div class="DateContainer_Date">
                                <xsl:value-of select="./DateObject/@shortMonth" />
                               <br/>
                               <xsl:value-of select="./DateObject/@date" />
                           </div>
                       </div>
                   </td>
                   <td>
                       <div class="UserInfo" >
                           <a class="UserName"/> 
                           <span> suggests going to </span>
                       </div>
                       <div class="Comment"></div>
                       <br/>
                   </td>
                <!--   <td>
                       <div data-toggle="popover" data-placement='top' id="tags"/>
                       <div class="popover fade top in">
                           <div class="arrow"></div>
                           <h3 class="popover-title"> Tags</h3>
                           <div class="popover-content">
                               <table>
                                   <tr>
                                       <td>
                                           <div data-toggle="tooltip" data-placement='top' title="Share with your friends" id="ShareEvent"/>
                                       </td>
                                       <td>
                                           <div data-toggle="tooltip" data-placement='top' title="Share with your friends" id="ShareEvent"/>
                                       </td>
                                   </tr>
                               </table>
                           </div>
                       </div>
                   </td> -->
                   <td>
                       <div data-toggle="tooltip" data-placement='top' title="Share with your friends" class="ShareEvent"/>
                   </td>
                   <td>
                       <div data-toggle="tooltip" data-placement='top' title="Favorite this event" class="FavoriteEvent" data-status="">
                          <div class="LocationAttendingCount">
                               <xsl:value-of select="count(./AttendanceManager/attendanceStatuses/yes/Member)" /> 
                           </div>
                       </div> 
                   </td>
               </tr>
       </table>
      </div>
      
  <!--  <a class="UserLink"><img class="UserPhoto" /></a>
    <div class="NameLocationContainer">
      <a class="UserLink"><span class="UserName"></span></a> <br />
      <span class="UserCollege" />
    </div>
    
    <div class="Comment"></div>
    
            <div class="Replies"></div>
            <xsl:if test="./Viewer/@userId != -1">
              <input class="replybox" type="text" name="replycomment" placeholder="write a reply..." />
            </xsl:if>
            <div class="LikeTimestampContainer">
              <div class="Timestamp"></div>
              <div class="Likes"> - <span class="LikeCount">0</span><span class="heart">❤</span></div>
            </div>
            <div class="clear"></div>-->
        </div><!-- </li> -->
        
        
        
    </xsl:template>
    
    
</xsl:stylesheet>
