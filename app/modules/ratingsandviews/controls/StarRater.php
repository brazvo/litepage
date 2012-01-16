<?php
final class StarRater extends Object {   
    
    // defaults
    private $rating = 0;
	
	private $rates = 0;
    
    private static $instances = 0;
    
    private $objectId;
    
    private $active = true;
    
    private $class = 'rater-object';
    
    private $message = '';
    
    private $width;
    
    private $widthCoeficient;
	
	private $cid;


    /**
     *
     * @param float $fRating / average rating of content
     */
    public function __construct( $fRating, $iRates = 0, $cid ) {
        ++self::$instances;
        $this->objectId = md5(__CLASS__ . self::$instances);        
        $this->rating = $fRating;
		$this->rates = $iRates;
        $this->setWidth( 90 );
		$this->cid = $cid;
    }
    
    ////////////////////////// setters
    
    /**
     * Sets CSS class to main container of Rater
     * @param type $sClassName
     * @return Rater 
     */
    public function setClass( $sClassName )
    {
        $this->class .= " {$sClassName}";
        return $this;
    }
    
    /**
     * Sets message to show on page
     * @param type $sMessage
     * @return Rater 
     */
    public function setMessage( $sMessage )
    {
        $this->message = $sMessage;
        return $this;
    }
    
    
    public function setWidth( $iWidth )
    {
        $this->width = $iWidth;
        $this->widthCoeficient = $iWidth / 5;
        return $this;
    }
    
    
    public function isActive( $bool = true )
    {
        $this->active = $bool;
        return $this;
    }
    
    
    
    ////////////////////////// render
    
    public function render()
    {
        if( $this->active ) {
            return (string) $this->renderActive();
        }
        else {
            return (string) $this->renderInactive();
        }
    }
    
    private function renderInactive()
    {
        $width = round( $this->rating * $this->widthCoeficient );
        
        $cont = Html::elem('div')->setClass( $this->class )->style("width:{$this->width}px;")->id( $this->objectId );
        if( $this->message ) $cont->title($this->message);
        
        $ratBack = Html::elem('div')->setClass('rating-background');
        
        if( $width > 0 ) {
            $actRating = Html::elem('div')->setClass('actual-rating');
            $actRating->style = "width: {$width}px; position:absolute; top:0; left:0;";
        }
        else {
            $actRating = '';
        }
        
        $ratBack->setCont( $actRating );
        $cont->setCont( '<p class="orange">Hodnotenie:</p>'.$ratBack."<p>hviezdičky: <b>{$this->rating}</b></p>". ($this->rates > 0 ? "<p>hodnotené: <b>{$this->rates}x</b></p>" : '' ) );
        
        return $cont;
        
    }
    
    private function renderActive()
    {
        $width = round( $this->rating * $this->widthCoeficient );
        
        $cont = Html::elem('div')->setClass( $this->class )->style("width:{$this->width}px;")->id( $this->objectId );
        
        $ratBack = Html::elem('div')->setClass('rating-background');
        
        if( $width > 0 ) {
            $actRating = Html::elem('div')->setClass('actual-rating');
            $actRating->style = "width: {$width}px; position:absolute; top:0; left:0;";
        }
        else {
            $actRating = Html::elem('div')->setClass('actual-rating');
            $actRating->style = "width: 0px; position:absolute; top:0; left:0;";
        }
        
        for ($i=1; $i<6; $i++) {
            $actRating->add( Html::elem('a')->setClass("rate-{$i}")->title("{$i} bod" . ($i > 1 ? ($i > 4 ? "ov" : "y") : "") )->href( baseUrl() . "/ratingsandviews/frontend/rate/{$this->cid}?am={$i}&amp;des=" . Environment::get( 'httpQuery' ) ) );
        }
        
        $ratBack->setCont( $actRating );
        
        //script
        $script = Html::elem('script'); $script->type = 'text/javascript';
        $script->setCont(
                "jQuery(document).ready(function($){
                
                    var ratWidth = {$width};
                    var widthCoef = {$this->widthCoeficient};
                    
                    function setWidth(width){
                        $('#{$this->objectId} .actual-rating').width(width);
                    }
                                      
                    $('a.rate-1').mouseover( function(){ setWidth(widthCoef); } );
                    $('a.rate-2').mouseover( function(){ setWidth(widthCoef*2); } );
                    $('a.rate-3').mouseover( function(){ setWidth(widthCoef*3); } );
                    $('a.rate-4').mouseover( function(){ setWidth(widthCoef*4); } );
                    $('a.rate-5').mouseover( function(){ setWidth(widthCoef*5); } );
                        
                    $('#{$this->objectId} .actual-rating a').mouseleave( function(){ setWidth(ratWidth); } );
                    
                    $('#{$this->objectId} .actual-rating a').click(function(){
                        
                        var href = $(this).attr('href');
                        $.ajax({
                            url: href+'&isajax=1',
                            context: document.body,
                            success: function(data){
                                var e = $(data).find('#{$this->objectId}');
                                var m = $(data).find('div.flash-messages');
                                var _m = document.createElement('div');
                                $(_m).css({'position':'absolute', 'top':'5px', 'left':'50%', 'width':{$this->width}-18+'px', 'margin-left':'-'+({$this->width}-18)/2+'px', 'border':'1px solid #444', 'background':'none #fff', 'padding':'2px'});
                                $(_m).append( $(m).html() );
                                $('#{$this->objectId}').css({'position':'relative'}).html( e.html() ).append(_m);
                                $(_m).delay(5000).fadeOut('slow');
                            }
                        });
                    
                        return false;
                    });
                    
                });"
        );
        $ajaxWrapper = Html::elem('div')->setClass('ajax-wrapper')->setCont( '<p class="orange">Hodnotenie:</p>'.$ratBack."<p>hviezdičky: <b>{$this->rating}</b></p>" . ($this->rates > 0 ? "<p>hodnotené: <b>{$this->rates}x</b></p>" : '' ) );
        $cont->setCont( $ajaxWrapper );
        
        return $cont . $script;
    }
    
    public function __toString() {
        return $this->render();
    }
}

