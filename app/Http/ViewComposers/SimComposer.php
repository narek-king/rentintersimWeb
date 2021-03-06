<?php
/**
 * Created by PhpStorm.
 * User: narek
 * Date: 11/30/16
 * Time: 7:10 PM
 */

namespace App\Http\ViewComposers;
use Illuminate\View\View;
use App\Models\Sim;
use App\Models\Provider;



class SimComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
//    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param
     * @return void
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
//        $this->users = $users;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $counts = ([
            'All' => Sim::all()->count(),
            'active' => Sim::filter('active')->count(),
            'pending' => Sim::filter('pending')->count(),
            'Available' => Sim::filter('available')->count(),
            'Parking' => Sim::filter('parking')->count(),
        ]);

        $view->with('viewName', $view->getName())
            ->with('counts', $counts)
            ->with('providers', Provider::select('id', 'name')->get()->toArray());
    }
}