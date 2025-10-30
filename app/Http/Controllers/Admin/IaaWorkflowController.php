<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IaaWorkflow;
use Illuminate\Http\Request;
use App\Models\IaaWorkflowStep;


class IaaWorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflows = \App\Models\IaaWorkflow::latest()->get();
        return view('admin.workflows.index', compact('workflows'));
    }

    // ... index() metodunun bittiği yer ...

    /**
     * Yeni bir akış şablonu oluşturma formunu gösterir.
     */
    public function create()
    {
        return view('admin.workflows.create');
    }

    /**
     * Yeni oluşturulan akış şablonunu veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        // 1. Gelen veriyi doğrula
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:iaa_workflows,name',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        // 2. "Varsayılan" olarak işaretlendiyse, diğer tüm şablonların işaretini kaldır.
        // Bu, sistemde sadece bir tane varsayılan şablon olmasını garantiler.
        if ($request->has('is_default')) {
            \App\Models\IaaWorkflow::query()->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        // 3. Yeni şablonu oluştur.
        \App\Models\IaaWorkflow::create($validated);

        // 4. Kullanıcıyı başarı mesajıyla ana listeye geri yönlendir.
        return redirect()->route('admin.workflows.index')
                         ->with('success', 'Yeni akış şablonu başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IaaWorkflow $iaaWorkflow)
    {
        //
    }

    /**
     * Belirtilen akış şablonunu düzenleme formunu gösterir.
     */
    public function edit(\App\Models\IaaWorkflow $workflow) // Route-model binding sayesinde $workflow otomatik olarak bulunur
    {
        return view('admin.workflows.edit', compact('workflow'));
    }

    /**
     * Veritabanındaki belirtilen akış şablonunu günceller.
     */
    public function update(Request $request, \App\Models\IaaWorkflow $workflow)
    {
        // 1. Veriyi doğrula. Unique kuralını, mevcut kaydı hariç tutacak şekilde güncelliyoruz.
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:iaa_workflows,name,' . $workflow->id,
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        // 2. "Varsayılan" olarak işaretlendiyse, diğer tüm şablonların işaretini kaldır.
        if ($request->has('is_default')) {
            \App\Models\IaaWorkflow::where('id', '!=', $workflow->id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        // 3. Kaydı güncelle.
        $workflow->update($validated);

        // 4. Kullanıcıyı başarı mesajıyla ana listeye geri yönlendir.
        return redirect()->route('admin.workflows.index')
                         ->with('success', 'Akış şablonu başarıyla güncellendi.');
    }


    /**
     * Bir iş akışı şablonunun adımlarını yönetme sayfasını gösterir.
     */
    public function editSteps(\App\Models\IaaWorkflow $workflow)
    {
        // Şablonun mevcut adımlarını 'order' sırasına göre alıyoruz.
        $steps = $workflow->steps()->orderBy('order')->get();
        return view('admin.workflows.edit-steps', compact('workflow', 'steps'));
    }

    /**
     * Bir iş akışı şablonuna yeni bir adım ekler.
     */
    public function storeStep(Request $request, \App\Models\IaaWorkflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_duration_days' => 'required|integer|min:1',
        ]);

        // Mevcut en son adımın sırasını bulup bir fazlasını alıyoruz.
        $lastOrder = $workflow->steps()->max('order') ?? 0;

        $workflow->steps()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'default_duration_days' => $validated['default_duration_days'],
            'order' => $lastOrder + 1,
        ]);

        return back()->with('success', 'Yeni adım başarıyla eklendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IaaWorkflow $iaaWorkflow)
    {
        //
    }

    /**
     * Bir iş akışı adımını günceller.
     */
    public function updateStep(Request $request, IaaWorkflowStep $step)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_duration_days' => 'required|integer|min:1',
        ]);

        $step->update($validated);

        return back()->with('success', 'Adım başarıyla güncellendi.');
    }

    /**
     * Bir iş akışı adımını siler.
     */
    public function destroyStep(IaaWorkflowStep $step)
    {
        // Silmeden önce, bu adımın ait olduğu şablonun ID'sini ve kendi sıra numarasını alalım.
        $workflowId = $step->iaa_workflow_id;
        $deletedOrder = $step->order;

        // Adımı sil.
        $step->delete();
        
        // Şimdi, aynı şablonda, silinen adımın sırasından daha büyük olan tüm adımları bul
        // ve onların 'order' değerini bir azalt.
        IaaWorkflowStep::where('iaa_workflow_id', $workflowId)
                    ->where('order', '>', $deletedOrder)
                    ->decrement('order');

        return back()->with('success', 'Adım başarıyla silindi ve sıra güncellendi.');
    }
}