<?php

// - Notifikasi Whatsapp
// - Perubahan batasan file menjadi 15Mb
// - Perubahan di JSA, Tambahkan metode di jenis anomali, Tambahkan foto di aspek lingkungan, tambahkan keterangan dan foto tambahan opsional di Aspek Kontruksi
// - JSA Gar

// ini yg masih belum diketahui bisa atau tidak nya.
// - Tambah Menu Untuk Merge PDF
// - File PPT yg mau dibuat (JSA)

// - Export Anomali Gardu Induk dan Jaringan ke Excel 
// - JSA
// - Notifikasi WA

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{AuthController, GarduIndukController, MethodToolController, OrganizationController, RoleController, ToolController, UserController, WarehouseController, WorkPlanController, PartnershipController, PusatInformasiController, HistoryToolController, JaringanController, LaporanPekerjaanController, OptionController, SpkiController};

Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }

    $dashboards = [
        1 => 'super.dashboard',
        2 => 'admin.dashboard',
        3 => 'asman.dashboard',
        4 => 'teamleader.dashboard',
        5 => 'jtc.dashboard',
    ];

    $roleId = Auth::user()->role_id;

    if (array_key_exists($roleId, $dashboards)) {
        return redirect()->route($dashboards[$roleId]);
    }

    // Default jika role tidak dikenal
    Auth::logout();
    return redirect()->route('login');
});

Route::get('coming-soon', fn() => view('auth.coming-soon'))->name('coming-soon');

Route::controller(AuthController::class)->group(function () {
    Route::get('sign-in', 'showLoginForm')->middleware('guest')->name('login');
    Route::post('sign-in', 'login');
    Route::post('sign-out', 'logout')->name('logout');
});


Route::middleware(['auth', 'is_active'])->group(function () {

    // Profile 
    Route::controller(AuthController::class)->prefix('profile')->name('user.profile')->group(function () {
        Route::get('/', 'profile');
        Route::post('/general', 'updateGeneral')->name('.general');
        Route::post('/password', 'updatePassword')->name('.password');
        Route::post('/signature', 'updateSignature')->name('.signature');
        Route::post('/certificates', 'updateCertificates')->name('.certificates');
    });


    // Common Routes
    $commonRoutes = function () {
        Route::get('dashboard', fn() => view('dashboard.index'))->name('dashboard');

        // SPKI
        Route::get('spki/get-org-data', [SpkiController::class, 'getOrganizationData'])->name('spki.get_org_data');
        Route::post('spki/{id}/approve', [SpkiController::class, 'approve'])->name('spki.approve');
        Route::post('spki/{id}/revision', [SpkiController::class, 'revision'])->name('spki.revision');
        Route::get('spki/{id}/export', [SpkiController::class, 'exportPdf'])->name('spki.export');
        Route::resource('spki', SpkiController::class);

        // Laporan Pekerjaan
        Route::get('laporan-pekerjaan/get-org-data', [LaporanPekerjaanController::class, 'getOrganizationData'])->name('laporan-pekerjaan.get_org_data');
        Route::post('laporan-pekerjaan/{id}/approve', [LaporanPekerjaanController::class, 'approve'])->name('laporan-pekerjaan.approve');
        Route::post('laporan-pekerjaan/{id}/revision', [LaporanPekerjaanController::class, 'revision'])->name('laporan-pekerjaan.revision');
        Route::get('laporan-pekerjaan/{id}/export', [LaporanPekerjaanController::class, 'exportPdf'])->name('laporan-pekerjaan.export');
        Route::resource('laporan-pekerjaan', LaporanPekerjaanController::class);

        // Anomali
        Route::put('gardu-induk/{id}/update-status', [GarduIndukController::class, 'updateStatus'])->name('gardu-induk.update-status');
        Route::resource('gardu-induk', GarduIndukController::class);

        Route::put('jaringan/{id}/update-status', [JaringanController::class, 'updateStatus'])->name('jaringan.update-status');
        Route::resource('jaringan', JaringanController::class);

        // Warehouse
        Route::controller(WarehouseController::class)->prefix('warehouse')->name('warehouse.')->group(function () {
            Route::get('export', 'export')->name('export');
            Route::post('{warehouse}/update-status', 'updateStatus')->name('updateStatus');
            Route::get('{warehouse}/tools', 'tools')->name('tools');
        });
        Route::get('warehouses-by-org/{organization}', [WarehouseController::class, 'getWarehousesByOrganization'])->name('warehouses.by_org');
        Route::resource('warehouse', WarehouseController::class);

        // Tool & History
        Route::get('tool/export', [ToolController::class, 'export'])->name('tool.export');
        Route::resource('tool', ToolController::class);

        Route::controller(HistoryToolController::class)->prefix('history-tool')->name('history-tool.')->group(function () {
            Route::get('resources', 'getResources')->name('get-resources');
            Route::post('{id}/return', 'markAsReturned')->name('return');
            Route::get('{id}/export', 'exportPdf')->name('export');
            Route::post('{id}/approve', 'approve')->name('approve');
        });
        Route::resource('history-tool', HistoryToolController::class);

        // Pusat Informasi
        Route::controller(PusatInformasiController::class)->group(function () {
            Route::prefix('ik-gardu-induk')->name('ik.gardu-induk.')->group(function () {
                Route::get('/', 'intruksiKerjaGI')->name('index');
                Route::post('/upload', 'uploadGI')->name('upload');
                Route::delete('/delete', 'deleteGI')->name('delete');
            });
            Route::prefix('ik-jaringan')->name('ik.jaringan.')->group(function () {
                Route::get('/', 'intruksiKerjaJaringan')->name('index');
                Route::post('/upload', 'uploadJaringan')->name('upload');
                Route::delete('/delete', 'deleteJaringan')->name('delete');
            });
        });
    };

    // Settings Routes (Method & Options)
    $settingRoutes = function () {
        Route::get('setting-method/get-tools', [MethodToolController::class, 'getToolsByOrganization'])->name('setting-method.get-tools');
        Route::resource('setting-method', MethodToolController::class);

        Route::controller(OptionController::class)->group(function () {
            $groups = [
                'history-tool' => 'HistoryTool',
                'spki' => 'SPKI',
                'laporan-pekerjaan' => 'LaporanPekerjaan',
                'ews' => 'EWS'
            ];

            foreach ($groups as $slug => $method) {
                Route::prefix("setting-$slug")->name("setting-$slug.")->group(function () use ($method, $slug) {
                    Route::get('/', "viewSetting$method")->name('view');
                    Route::post('/', "updateSetting$method")->name('update');
                    Route::get('/get-data', "getSetting$method")->name("get-data-$slug");
                });
            }
        });
    };

    // Work Plan Routes
    $workPlanRoutes = function () {
        Route::controller(WorkPlanController::class)->prefix('work-plan')->name('work-plan.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/list-by-year', 'listByYear')->name('listByYear');
            Route::get('/export', 'exportExcel')->name('export');
            Route::put('/{workPlan}', 'update')->name('update');
            Route::delete('/{workPlan}', 'destroy')->name('destroy');
        });
    };

    // User & Partnership Routes
    $userPartnershipRoutes = function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('user/export_data', 'export')->name('user.export');
            Route::post('user/{user}/update-status', 'updateStatus')->name('user.updateStatus');

            Route::prefix('user/{user}/certificates')->name('user.certificates.')->group(function () {
                Route::get('/', 'certificatePage')->name('index');
                Route::post('/', 'storeSingleCertificate')->name('store');
                Route::post('/{certificate}', 'updateSingleCertificate')->name('update');
                Route::delete('/{certificate}', 'destroySingleCertificate')->name('destroy');
            });
        });
        Route::resource('user', UserController::class);

        Route::get('partnership/export_data', [PartnershipController::class, 'export'])->name('partnership.export');
        Route::resource('partnership', PartnershipController::class);
    };

    // Role 1: Super Admin
    Route::middleware(['role:1'])->prefix('super')->name('super.')->group(function () use ($commonRoutes, $settingRoutes, $workPlanRoutes, $userPartnershipRoutes) {
        $commonRoutes();
        $settingRoutes();
        $workPlanRoutes();
        $userPartnershipRoutes();

        Route::get('organization/{organization}/users', [OrganizationController::class, 'getUsers'])->name('organization.users');
        Route::resource('organization', OrganizationController::class);
        Route::resource('role', RoleController::class)->only(['index', 'show', 'update']);
    });

    // Role 2: Admin Sistem
    Route::middleware(['role:2'])->prefix('admin')->name('admin.')->group(function () use ($commonRoutes, $settingRoutes, $workPlanRoutes, $userPartnershipRoutes) {
        $commonRoutes();
        $settingRoutes();
        $workPlanRoutes();
        $userPartnershipRoutes();
    });

    // Role 3: Assistant Manager
    Route::middleware(['role:3'])->prefix('asman')->name('asman.')->group(function () use ($commonRoutes, $workPlanRoutes) {
        $commonRoutes();
        $workPlanRoutes();
    });

    // Role 4: Team Leader
    Route::middleware(['role:4'])->prefix('team-leader')->name('team-leader.')->group(function () use ($commonRoutes, $workPlanRoutes) {
        $commonRoutes();
        $workPlanRoutes();
    });

    // Role 5: JTC
    Route::middleware(['role:5'])->prefix('jtc')->name('jtc.')->group(function () use ($commonRoutes, $workPlanRoutes) {
        $commonRoutes();
        $workPlanRoutes();
    });
});
