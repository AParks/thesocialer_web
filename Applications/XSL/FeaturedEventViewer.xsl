<?xml version="1.0"?>


<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    
    <xsl:template match="/FeaturedEventViewer">
        <div class="LocationViewer" id="MainBody">
            <div id="topcontainer"></div>
            <div id="leftcontainer">
                <div id="FeaturedEvent">    
                    <div class="EventTitle">
                        <h2>
                            <xsl:value-of select="./FeaturedEvent/@headline"/> 
                        </h2>
                        <h4>
                            <xsl:value-of select="./FeaturedEvent/@sub_headline"/> 
                        </h4>  
                       
                    </div>
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
                
                <ul class="nav nav-tabs">
                    <li class='active' >
                        <a href="#details" data-toggle="tab">Details</a>
                    </li>
                    <li>
                        <a href="#comments" data-toggle="tab">Comments</a> 

                    </li>
                    
                    <xsl:if test="./FeaturedEvent/@spots_purchased &gt;= 0.25*./FeaturedEvent/@total_spots">

                        <li>
                            <a href="#attending" data-toggle="tab">Attending
                                (<xsl:value-of select="./FeaturedEvent/@spots_purchased" />)

                            </a>
                        </li>
                    </xsl:if>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane  active" id="details">
                        <div id='desc'>
                            <span id="Description">
                            </span>
                         <!--   <xsl:if test="string-length(./FeaturedEvent/@description) &gt; 200" >
                                <span>
                                    <a id='SeeMore'>...See more </a>
                                </span>
                            </xsl:if> 

                        
                            <span id="More_Description"></span> -->
                        </div>
               
                    </div>
                    <div class="tab-pane" id="comments">
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
                    <div class='tab-pane' id='attending'>
                        
                        <div class="Attendees">
                            <div class="AttendeesYes">
                                <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/Member" />
                            </div>
                        </div>
                    </div>
                </div>
                           
                
            </div>
            <div id="rightcontainer">
                <div class="LocationInfoContainer">
            
                    <div class='booking'>
                        <div id="date">
                            <xsl:value-of select="./DateObject/@shortDay" />.

                            <xsl:value-of select="./DateObject/@shortMonth" />
                            <xsl:text disable-output-escaping="yes">&amp;</xsl:text>nbsp;
                            <xsl:value-of select="./DateObject/@date" />
                            @
                            <xsl:value-of select="./DateObject/@time" />
                        </div>
                        <span id="dollar_sign">$</span>
                        <span id="total_price">
                            <xsl:value-of select="./FeaturedEvent/@price" /> 
                        </span>
                        <div id='fee_text'>
                            + $<span id='fee'></span> in processing fees
                        </div>
                        
                        <br/>
                        
                        <xsl:choose>
                            <xsl:when test="./FeaturedEvent/@total_spots &lt;= ./FeaturedEvent/@spots_purchased">
                                <div class="joinbutton" id='soldout' >Sold Out</div>
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
                                <span id='fee_text'>
                                    out of <xsl:value-of select="./FeaturedEvent/@total_spots - ./FeaturedEvent/@spots_purchased" />
                                    available
                                </span>

                                <xsl:choose>

                                    <xsl:when test="./Viewer/@userId != -1">                                
                                        <button id="payButton" class="joinbutton">Book Now

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
                                        <button id="not-logged-in-payButton" class="joinbutton">Book Now</button>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:otherwise>
                        </xsl:choose>
                        
                        
                        <div class="encrypted">
                            <img height='15' src='/Static/Images/lock-white.png'/>
                            128-bit SSL Encrypted Transactions</div>                            
                        <!--                        <img alt="Credit Cards" src="/Static/Images/creditcards.gif"/> -->
                    </div> 
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
                    
                   
                    <ul class="nav nav-tabs">
                        <li class='active'>
                            <a href="#host" data-toggle="tab">Host</a>
                        </li>
                    </ul>
                    <xsl:apply-templates select="./Host/Member" />

                    <ul class="nav nav-tabs">
                        <li class='active'>
                            <a href="#location" data-toggle="tab">Location</a>
                        </li>
                    </ul>
                    <div id="LocationMap"></div>


                </div>
                                    
            </div>
            <div class="clear"></div>
        </div>

    </xsl:template>

    <xsl:template match="Host/Member">
        <div id='host'>
            <a data-user-id="{./@userId}" href="/profile/{./@userId}">
                <img src="/photo/{./@userId}/Medium" class="UserPhoto" />
            </a>
            <div>
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
