<?xml version="1.0"?>


<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    
    <xsl:template match="/FeaturedEventViewer">
        <div class="LocationViewer" id="MainBody">
            <div id="leftcontainer">
                <div class="EventTitle" id='title'>
                     <p id='head'>
                         <xsl:value-of select="./FeaturedEvent/@headline"/>
                     </p>
                     <p id='head'>
                         <xsl:value-of select="./FeaturedEvent/@sub_headline"/>
                     </p> 
                    </div>
                <div class="EventInfo">
                    
                   <div id='deets'>
                    <div>
                        <i class="icon-time"></i>
                        <div id="date">
                            <xsl:value-of select="./DateObject/@shortDay" />.

                            <xsl:value-of select="./DateObject/@shortMonth" />
                            <xsl:text disable-output-escaping="yes">&amp;</xsl:text>nbsp;
                            <xsl:value-of select="./DateObject/@date" />
                            @
                            <xsl:value-of select="./DateObject/@time" />
                        </div>
                    </div>
                    <div>
                        <i class="icon-map-marker"></i>
                        <div id='loc'>
                            <xsl:value-of select="FeaturedEvent/@location" disable-output-escaping="yes" />
                         </div>
                    </div>
                    <div>
                        <i class="icon-user"></i>

                        <span id='spots_avail'>
                            <xsl:choose>
                    <xsl:when test='./FeaturedEvent/@total_spots &lt; 0'>
                    &#8734;
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="./FeaturedEvent/@total_spots - ./FeaturedEvent/@spots_purchased"/>
                    </xsl:otherwise>
                </xsl:choose> available spots</span>
                    </div>
                    
                   </div>
                 
                        <div class='booking'>
                  
                        <!-- <div id='fee_text'>
                            + $<span id='fee'></span> in processing fees
                        </div> -->
                        
                        <xsl:choose>
                            <xsl:when test="./FeaturedEvent/@total_spots &lt;= ./FeaturedEvent/@spots_purchased">
                                <div class="joinbutton" id='soldout' >Sold Out</div>
                            </xsl:when>
                            <xsl:when test="./FeaturedEvent/@price = 0">
                                 <select type="number">
                                    <option value="1" selected="selected">1 spot</option>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 1">
                                        <option value="2">2 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 2">
                                        <option value="3">3 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 3">
                                        <option value="4">4 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 4">
                                        <option value="5">5 spots</option>
                                    </xsl:if>
                                </select>
                                <button id="freeButton" > Enjoy for free</button>
                            </xsl:when>
                            <xsl:otherwise>
                                
                                <select type="number">
                                    <option value="1" selected="selected">1 spot</option>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 1">
                                        <option value="2">2 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 2">
                                        <option value="3">3 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 3">
                                        <option value="4">4 spots</option>
                                    </xsl:if>
                                    <xsl:if test="./FeaturedEvent/@total_spots &gt; ./FeaturedEvent/@spots_purchased + 4">
                                        <option value="5">5 spots</option>
                                    </xsl:if>
                                </select>
                                

                                <xsl:choose>

                                    <xsl:when test="./Viewer/@userId != -1">                                
                                        <button id="payButton" class="joinbutton"> Enjoy for
                                            $<span id="total_price">
                                                <xsl:value-of select="./FeaturedEvent/@price" /> 
                                            </span>

                                            <form id='charge' action="/charge" method="post">
                                                <script
                                                    src="https://checkout.stripe.com/v2/checkout.js" 
                                                    data-key="pk_test_tPl6A15XRwUWmiz0bEB280hN"
                                                    data-amount=""
                                                    data-name=""
                                                    data-description=""
                                                    data-currency="usd">
                                                </script>
                                                <input type='hidden' name='email'>
                                                    <xsl:attribute name="value">
                                                        <xsl:value-of select="./Viewer/Member/@emailAddress" />
                                                    </xsl:attribute>
                                                </input>
                                                <input type='hidden' name='userId'>
                                                    <xsl:attribute name="value">
                                                        <xsl:value-of select="./Viewer/@userId" />
                                                    </xsl:attribute>
                                                </input>

                                                <input type='hidden' name='featured_event_id'>
                                                    <xsl:value-of select="./FeaturedEvent/@featured_event_id" />
                                                </input> 
                                            </form>
                                        </button>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <button id="not-logged-in-payButton" class="joinbutton">Enjoy for
                                             $<span id="total_price">
                                                <xsl:value-of select="./FeaturedEvent/@price" /> 
                                            </span>
                                        </button>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:otherwise>
                        </xsl:choose>
                        
                        
                        <div class="encrypted"><i class="icon-lock"></i> 
                            Your transactions are secure</div>                            
                        <!--                        <img alt="Credit Cards" src="/Static/Images/creditcards.gif"/> -->
                    </div>
                    <br/> 
                      <p id='desc'>
                        <!--  <xsl:value-of select="FeaturedEvent/@description" disable-output-escaping="yes" />-->
                      </p>
                      <div id="FeaturedEvent">    
                    
                    <div id="myCarousel" class="carousel slide">
                        <ol class="carousel-indicators">
                            <!--    <li data-target="#myCarousel" data-slide="next" class="active"></li>-->
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">

                        </div>
                        <!-- Carousel nav -->
                        <a class="carousel-control left" href="#myCarousel" data-slide="prev">
                            <xsl:text disable-output-escaping="yes">&#8249;</xsl:text>

                        </a>

                        <a class="carousel-control right" href="#myCarousel" data-slide="next">
                            <xsl:text disable-output-escaping="yes">&#8250;</xsl:text>

                        </a>
                    </div>

                    <!-- <div id="FeaturedEvent_Markup">
                        <xsl:value-of select="FeaturedEvent/@location" disable-output-escaping="yes" />
                    </div>   -->
                    
                    
                </div>
                </div>
                
                
                        <div id='title'>Comments</div> 
                <div id='Comments'>
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
               <!-- <ul class="nav nav-tabs">
                    <li class='active' >
                        <a href="#details" data-toggle="tab">Details</a>
                    </li>
                    <li>
                        <a href="#comments" data-toggle="tab">Comments</a> 
                    </li>
                    
                   
                </ul> 
          <div class="tab-content">
                    <div class="tab-pane  active" id="details">
                        <div id='desc'>
                            <span id="Description">
                            </span>
                        <xsl:if test="string-length(./FeaturedEvent/@description) &gt; 200" >
                                <span>
                                    <a id='SeeMore'>...See more </a>
                                </span>
                            </xsl:if> 

                        
                            <span id="More_Description"></span>
                        </div>
               
                    </div>
                    <div class="tab-pane" id="comments">
                    
                    </div> 
                </div>
                           
            -->
        </div>
        <div id="rightcontainer">
            <div class="LocationInfoContainer">
            
                <br/>
                    
                <button id='fb-share'>
                    <div id='img'/>
                    <a id='share' href="https://www.facebook.com/sharer/sharer.php?u=thesocialer.com/popups/featured/{./FeaturedEvent/@featured_event_id}" target="_blank">                        
                        Share
                    </a>
                </button>
                <div class='count-o'>
                    <i></i>
                    <u></u>
                    <a id='share-count'></a>
                </div>
    
                <a href="https://twitter.com/share" class="twitter-share-button" data-related="socialerphilly" data-via="socialerphilly" data-text="{./FeaturedEvent/@headline}" data-size="large">Tweet</a>

                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
   
                
                <xsl:apply-templates select="./Host/Member" />
                <xsl:if test="./FeaturedEvent/@spots_purchased &gt; 0">
                    <div id='title' class='loc'>
                        Attending (<xsl:value-of select="./FeaturedEvent/@spots_purchased" />)
                    </div>

                    <div class="AttendeesYes">
                        <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/Member" />
                    </div>
                        
                </xsl:if>
                    
                    
                    <div id='title' class='loc'>Location</div>
                <div id='location' >
                    <div id="LocationMap"></div>
                </div>
            </div>
                                    
        </div>
        <div class="clear"></div>
    </div>

    </xsl:template>

    <xsl:template match="Host/Member">
        <div id='title'>Host
                <span id='interact-with-host'>
                    <xsl:choose>
                        <xsl:when test="../../Viewer/@userId != -1">                                
                        
                            <i id="SendMessage" name="{./@firstName}" class="icon-envelope icon-large"></i>
                            <xsl:text disable-output-escaping="yes">&amp;</xsl:text>nbsp;
                            <i class="icon-star icon-large"></i>

                        </xsl:when>
                        <xsl:otherwise>
                            <i id="notLoggedInSendMessage" name="{./@firstName}" class="icon-envelope icon-large"></i>
                            <xsl:text disable-output-escaping="yes">&amp;</xsl:text>nbsp;
                            <i class="icon-star icon-large"></i>

                        </xsl:otherwise>
                    </xsl:choose>
                </span>

            </div>
        <div id='host'>
            
            <a data-user-id="{./@userId}" href="/profile/{./@userId}">
                <img width='80px' src="/photo/{./@userId}/Medium" class="UserPhoto" />
            </a>
            <div id='right'>
                <div id='name'  style='vertical-align: text-top'>
                    <xsl:value-of select="./@firstName"/>
                    <xsl:text disable-output-escaping="yes">&amp;</xsl:text>nbsp;

                    <xsl:value-of select="./@lastName"/>
                </div>
                <xsl:value-of select="./@AboutMe"/>
            </div>
        </div>
       
    </xsl:template>



    <xsl:template match="Member">

        <xsl:if test="/FeaturedEventViewer/FeaturedEvent/@host != ./@userId">
            <xsl:choose>
                <xsl:when test="/FeaturedEventViewer/FeaturedEventAttendanceManager/attendanceStatuses/Member/@userId = /FeaturedEventViewer/Viewer/@userId">
                    <a data-user-id="{./@userId}" href="/profile/{./@userId}">
                        <img src="/photo/{./@userId}/Medium" class="UserPhoto" />
                    </a>
                </xsl:when>
                <xsl:otherwise>
                    <a data-user-id="{./@userId}">
                        <img src="/photo/{./@userId}/Medium" class="UserPhoto" />
                    </a>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
