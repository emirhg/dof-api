<?php namespace IMCO\CatalogoNOMsApi;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DateTime;

class NMXController extends Controller {
	protected $connection = 'catalogoNoms';
	
	/**
	 *		@SWG\Path(
	 *			path = "/catalogonoms/nmx/vigentes",
	 *			@SWG\Get(
	 *				summary = "Resumen de las normas vigentes",
	 *				tags = {"CatalogoNMX"},
	 *				@SWG\Response(response = "200", description = "JSON de respuesta", @SWG\Schema(type = "json"))
	 * 			)
	 *		)
	 */

	public function getNMXVigentes(){
		$vigentes = NormaVigente::with(['menciones'=>function ($query){
				$query->where('etiqueta', 'Vigencia')->with(['nota'=>function ($query){
					$query->select('titulo', 'cod_nota', 'cod_diario')->with('diario');
				}]);
			}])->orderBy('clave')->get();	
		return \Response::json($vigentes);
	}


	/**
	 *		@SWG\Path(
	 *			path = "/catalogonoms/nmx/datelle/{clave}",
	 *			@SWG\Get(
	 *				summary = "Resumen de las normas vigentes",
	 *				tags = {"CatalogoNMX"},
	 *				@SWG\Parameter(
	 * 					name="clave",
	 * 					in="path",
	 * 					required=true,
	 * 					type="string",
	 *					default = "NMX-A-001-1965",
	 * 					description="Clave de la Norma"
	 *				),
	 *				@SWG\Response(response = "200", description = "JSON de respuesta", @SWG\Schema(type = "json"))
	 * 			)
	 *		)
	 */

	public function getNMXDetalle($clave){
		$norma = IMCO\CatalogoNOMsApi\NormaVigente::with(['menciones.nota'=>function ($query){
			$query->select('titulo', 'cod_nota', 'cod_diario')->with('diario');
		}])->where('clave', $clave)->first();
		return \Response::json($norma);
		
	}

}
