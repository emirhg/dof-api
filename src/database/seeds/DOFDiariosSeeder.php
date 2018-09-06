<?php namespace IMCO\CatalogoNOMsApi;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use IMCO\CatalogoNOMsApi;

use Response;

class DOFDiariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dofClient = new DOFClientController;
        print_r('Descargando códigos de diarios...');

        $year = date("Y");
        for ($month= date("m"); $month>0 ; $month--){
          $dofDiario = $dofClient->getEditionsOnDate($year, $month)->getData();
          foreach($dofDiario->list as $diario){
            $diario->fecha = DOFClientController::reformatDateString($diario->fecha);
            print_r($diario->fecha . "\n");
            DofDiario::firstOrCreate((array)$diario);
          }
        }

        for ($year = date("Y")-1; $year>=1917 ; $year--){
            $dofDiario = $dofClient->getEditionsOnDate($year)->getData();
            foreach($dofDiario->list as $diario){
                $diario->fecha = DOFClientController::reformatDateString($diario->fecha);
                print_r($diario->fecha . "\n");
                DofDiario::firstOrCreate((array)$diario);
            }
        }

        print_r('Diarios seeded');
	}
}
