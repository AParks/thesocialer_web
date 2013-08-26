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

    <xsl:template match="/Landing">
        <div id='top' style='text-align: center'>
        <div id='circle'>
                <div id="myCarousel" class="carousel slide">
                        
                        <div class="carousel-inner">
                            <div class='item active' >
                                 <div class='community' style='background-image: url(/Static/Images/Aneta-Ivanova1.jpg)'/>
                            </div>    
                            <div class='item'>
                                 <div class='community' style='background-image: url(https://www.filepicker.io/api/file/tYSSbhJIQu8LfXAoujfW)'/>
                            </div>
                            <div class='item'>
                                <div class='community' style='background-image: url(/Static/Images/circle.jpg)'/>
                            </div>
                            <div class='item'> 
                                 <div class='community' style='background-image: url(https://www.filepicker.io/api/file/Q439iJ3hS0ekqiZMhIgx)'/>
                            </div>
                            <div class='item'>
                                <div class='community' style='background-image: url(https://www.filepicker.io/api/file/kz76JO36R5aOYk9PWCHf)'/>
                            </div>
                        </div>
                </div>
            </div>
        <div id='img'>
            
        <!--    <div id='container-black'></div>-->
        
            <div id='container'>
            <div id='popup-desc-start'>Expl<span class='yellow'>o</span>re <span class='red'>o</span>utside y<span class='green'>o</span>ur circle<span class='blue'>.</span></div><br/>
            <div id='popup-desc'>

                Try something different. Meet someone interesting.
              <!--  <br/>If you have an idea you're passionate about, <br/>contact <strong>concierge@thesocialer.com</strong> to be a host.-->
                
            </div>
           
            
            
                        
            
          <!--  <xsl:choose>
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
                 <a class='create-popup' href='/popups/new'>
                <div id='new_popup_button' class='new_popup_button'>
                <i class="icon-edit"></i>
                Create a new popup</div>
                 </a> <br/>
                 <div id='popup-safety'>
                   <div id='verified'>
                         <i class="icon-ok-sign icon-large"></i>
                     </div>
                     Safety guaranteed. BLAH All hosts and events are personally verified by the Socialer team.
                    <br/>
                         <i class="icon-envelope  icon-large"></i>
                     
                     Get to know each other before the event via our messaging system
                     <br/>
                         <i class="icon-lock  icon-large"></i>
                     Pay and get paid via Stripe, a secure payments system.
                 </div>
            </xsl:otherwise>
            </xsl:choose>-->
            </div>
           
        </div>
        </div>
        
      <!--   <div id='become-host'>
            <div id='how-it-works'>
                <div id='text'>1. Create a popup.</div>
                <div class='community'>
                   <i class="icon-edit icon-large"></i>
                </div>
                
            </div>
            <div id='how-it-works'>
                <div id='text'>2. Invite people inside and outside your circle.</div>
                <div class='community' style='background-image: url(/Static/Images/circle.jpg)'></div>


                
            </div>
            <div id='how-it-works'>
                <div id='text'>3. Share your passion</div>
                <div class='community' style='background-image: url(http://3.bp.blogspot.com/-Pa58x-XmSk8/UeYm3N4MSnI/AAAAAAAACYg/P6gw_lPI-bY/s1600/Aneta-Ivanova1.jpg)'></div>

            </div>
            
            
        </div>
        
        
        
       <div id='engage'>
            <div 
                class='community first' style='background-image: url(https://www.filepicker.io/api/file/kz76JO36R5aOYk9PWCHf)'></div>
            <div class='community' style='background-image: url(https://www.filepicker.io/api/file/Q439iJ3hS0ekqiZMhIgx)'></div>
        </div>-->
        
        <div id='host-discover'>
             <div id='partition' class='box'>
                <h2>Discover Popups</h2>
                <div>
                    <div class='iconContainer yellow'>
                        <i class="icon-search"></i>
                    </div> 
                    <div class='list-item'>Learn, make, play - experience something <strong>new</strong>.</div>
                </div>
                <div>
                    <div class='iconContainer yellow'>
                        <i class="icon-user"></i>
                    </div>
                        <div class='list-item'>
                            See who's going, bring a friend or come solo, and meet someone <strong>outside your</strong> personal <strong>networks</strong>.
                        </div>
                </div>
                <div>
                    <div class='iconContainer yellow'>
                        <i class="icon-credit-card"></i>
                    </div>
                    <div class='list-item'>
                        Make seamless and secure payments.
                    </div>
                </div>
                <a class='discover-now' href='/popups'>
                    <div id='new_popup_button' class='new_popup_button' style='background-image: none; background-color: #db9729'>
                        <i class="icon-search"></i>
                        Discover now
                    </div>
                </a>
            </div>
            <div id='partition' class='box'>
                <h2>Create a popup</h2>
                <div>
                    <div class='iconContainer green'>
                        <i class="icon-edit"></i>
                    </div>
                    <div class='list-item'>
                        Easily create experiences to share your <strong>passion</strong> and skills.
                    </div>
                </div>
                <div>
                    <div class='iconContainer green'>
                        <i class="icon-envelope"></i>
                    </div>
                    <div class='list-item'>
                        We'll broadcast to interesting people <strong>outside your network</strong>. You control who can attend.
                    </div>
                </div>
                <div>
                    <div class='iconContainer green'>
                        <i class="icon-dollar"></i>
                    </div>
                    <div class='list-item'>
                    Get <strong>paid</strong>, or host something for free. 
                    </div>
                </div>
                
            <xsl:choose>
                <xsl:when test='./Viewer/@userId != -1'>
                    <a class='host-now' href='/popups/new'>
                        <div id='new_popup_button' class='new_popup_button' style='background-image: none; background-color: #79a161'>
                            <i class="icon-edit"></i>
                            Host now
                        </div>
                    </a>
                </xsl:when>
                <xsl:otherwise>
                        <div id='new_popup_button' class='host-now notLoggedIn new_popup_button' style='background-image: none; background-color: #79a161'>
                            <i class="icon-edit"></i>
                            Host now
                        </div>
                </xsl:otherwise>
            </xsl:choose>
            </div>
             <div id='partition' class='box'>

            <h2>Trust and Safety</h2>
             <div id='popup-safety'>
                  <div class='iconContainer blue'>
                         <i class="icon-ok-sign "></i>
                    </div>
                    <div class='list-item'>
                        All hosts and events are personally verified by the Socialer team.
                    </div>
                    <div class='iconContainer blue'>
                        <i class="icon-envelope"></i>
                    </div>
                    <div class='list-item'>
                        Get to know each other before the event via our messaging system
                    </div>
                    <div class='iconContainer blue'>
                        <i class="icon-lock"></i>
                    </div>
                                        <div class='list-item'>

                     Pay and get paid via Stripe, a secure payments system.
                                            </div>
                     <div class='iconContainer blue'>
                     <i class="icon-phone-sign blue"></i>
                    </div>
                    <div class='list-item'>
                                             Contact us anytime about anything at concierge@thesocialer.com

                     </div>
                 </div>
                 </div>
             
            
        </div>
        <div id='discover'>
            <h2>Featured Popups</h2>
            <ol id="LocationList">
                                                            
                <xsl:for-each select="images/image">

                    <li class="loc">
                        <div class="TitleContainer">
                            <div class="EventTitle">
                                <div id='title'><xsl:value-of select="./@headline"/></div>
                     
                               
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
        <div id='trust'>
                        
            
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
