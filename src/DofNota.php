<?php namespace IMCO\CatalogoNOMsApi;


use Illuminate\Database\Eloquent\Model;
use DOMDocument;

class DofNota extends Model
{
    protected $connection = 'CatalogoNoms';
    protected $primaryKey = 'cod_nota';
    protected $fillable = array('cod_diario', 'cod_nota', 'titulo', 'contenido', 'pagina', 'secretaria', 'organismo', 'seccion');

    public function updateTitulo(){
    	$decretoFull = NULL;
    	$tries = 5;
    	while (strlen($decretoFull)<=1 && $tries>0){
			$decretoFull = DOFClientController::http_get('http://diariooficial.gob.mx/nota_detalle_popup.php?codigo='. $this->cod_nota);

			//print_r('http://diariooficial.gob.mx/nota_detalle_popup.php?codigo='. $this->cod_nota . "\n");
			$matches = array();
			preg_match('/<!DOCTYPE HTML .* <\/HTML>/', $decretoFull, $matches);

			if (!$matches && strlen($decretoFull)>0){
				$matches[0] = $decretoFull;
			}
			if ($matches){
				$decretoFull = $matches[0];
				$decretoFull = $testHTML = preg_replace('/(&#\d{4});?/', '\1;', $decretoFull);
				
				try{
					$decretoDOM = new \DOMDocument();
					libxml_use_internal_errors(true);

					$decretoDOM->loadHtml($decretoFull);
					libxml_clear_errors();

					$h1 = $decretoDOM->getElementsByTagName('h1');
					if ($h1->length >0 ){
						$this->titulo = trim(preg_replace('/\s+/',' ',html_entity_decode($h1->item(0)->nodeValue)));
					}else{
						$font = $decretoDOM->getElementsByTagName('font');
						if ($font->length >0 ){
							for ($i=0; $i<$font->length;$i++){
								$found = preg_replace('/\s+/', ' ', trim(strtolower(preg_replace('/[^\w\s]/', '', html_entity_decode($font->item($i)->nodeValue)))));
								$current = preg_replace('/\s+/', ' ', trim(strtolower(preg_replace('/[^\w\s]/', '',$this->titulo))));

								//print_r("$found\n$current\n". strcmp($found,$current) . "\n");
								if (strcmp($found,$current)==0){
									$this->titulo = trim(preg_replace('/\s+/',' ',html_entity_decode($font->item($i)->nodeValue)));
									break;
								}
							}
						}
					}
				} catch(ErrorException $exception ){

				}
			}

			$tries--;
		}
		$this->save();
    }
}