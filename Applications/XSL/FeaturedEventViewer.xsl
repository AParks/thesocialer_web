<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:soc="http://kemmerer.co">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/FeaturedEventViewer">
        <div class="LocationViewer" id="MainBody">
            <div id="topcontainer">
               
            </div>
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
                        <select type="number">
                            <option value="1" selected="selected">1 spot</option>
                            <option value="2" style="font-family: comfortaaregular">2 spots</option>
                            <option value="3">3 spots</option>
                            <option value="4">4 spots</option>
                            <option value="5">5 spots</option>
                        </select>
                        
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
                        <img alt="Credit Cards" src="/Static/Images/creditcards.gif"/>
                    </div> 
                    <div class="encrypted">128-bit SSL Encrypted Transactions</div>

                   
            
                    <!--    <h1>About The Event:</h1>
                    <h2>Time</h2>
                    <p id="FeaturedTime"></p>
                    <h2>Address:</h2>
                    <p>
                        <xsl:value-of select="./FeaturedEvent/@markup" disable-output-escaping="yes" />
                    </p> -->
                    <div class="Attendees">
                        <div class="AttendeesYes">
                            <h2>Attending:</h2>
                            <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/Member" />
                        </div>
                        <!--               <div class="AttendeesMaybe">
                            <strong>Maybe</strong>
                            <xsl:apply-templates select="./FeaturedEventAttendanceManager/attendanceStatuses/maybe/Member" />
                        </div> -->
                    </div>
                    
                    <h2>Host:</h2>
                    <a data-user-id="{./FeaturedEvent/@host}" href="/profile/{./FeaturedEvent/@host}">
                        <img src="/photo/{./FeaturedEvent/@host}/Medium" class="UserPhoto" />
                    </a>

                    <h2>Location:</h2>

                    <div id="LocationMap"></div>
                </div>
                                    
            </div>
            <div class="clear"></div>
        </div>

    </xsl:template>

    <xsl:template match="Member">
        <a data-user-id="{./@userId}" href="/profile/{./@userId}">
            <img src="/photo/{./@userId}/Small" class="UserPhoto" />
        </a>
    </xsl:template>
</xsl:stylesheet>
