<?php

namespace Database\Seeders;

use App\Models\OrganizationPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HistoriqueContentSeeder extends Seeder
{
    public function run(): void
    {
        // Page 1: Introduction et Histoire
        OrganizationPage::updateOrCreate(
            ['slug' => 'historique-introduction'],
            [
                'title' => 'Présentation de l\'Université',
                'content' => '<div class="space-y-4">
<p class="text-lg leading-relaxed">L\'Université de Mahajanga est régie par le <strong>Décret 2002-565 du 04 juillet 2002</strong>. Elle dispose actuellement de 2 Facultés, 5 Instituts, 4 Écoles, 1 unité de Formation et de Recherche, 1 Musée régional et 3 Écoles doctorales.</p>

<p class="leading-relaxed">La diversification de ces formations vise à répondre aux besoins nationaux en compétence professionnelle et également à permettre l\'accès aux jeunes à des études approfondies. L\'Université de Mahajanga donne aux étudiants un ample choix de parcours de formation et de professionnalisation.</p>
</div>',
                'page_type' => 'historique',
                'order' => 1,
                'is_published' => true,
                'meta_title' => 'Historique - Université de Mahajanga',
                'meta_description' => 'Découvrez l\'histoire de l\'Université de Mahajanga depuis sa création.',
            ]
        );

        // Page 2: Les grandes dates
        OrganizationPage::updateOrCreate(
            ['slug' => 'historique-grandes-dates'],
            [
                'title' => 'Les grandes dates de notre histoire',
                'content' => '<div class="space-y-6">
<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">1896</p>
    <p class="font-medium">Origines de l\'Enseignement Supérieur</p>
    <p class="text-slate-600">Création d\'une école de médecine à Befalatana, puis d\'écoles de droit, de sciences et de lettres.</p>
</div>

<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">1960</p>
    <p class="font-medium">Fondation de l\'Université de Madagascar</p>
    <p class="text-slate-600">La réunion de ces établissements est érigée en université.</p>
</div>

<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">1976-1978</p>
    <p class="font-medium">Décentralisation et Malgachisation</p>
    <p class="text-slate-600">L\'ordonnance du 27 décembre 1976 définit les nouvelles structures : démocratisation de l\'accès à l\'enseignement supérieur, malgachisation et décentralisation. La loi du 17 juillet 1978 fixe le cadre général du système d\'éducation et de formation.</p>
</div>

<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">1977</p>
    <p class="font-medium">Création des Centres Universitaires Régionaux</p>
    <p class="text-slate-600">Il était créé dans chaque chef-lieu des six provinces de Madagascar, un Centre Universitaire Régional (CUR). L\'ensemble des six CUR formait l\'Université de Madagascar.</p>
</div>

<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">7 octobre 1988</p>
    <p class="font-medium">Naissance de l\'Université de Mahajanga</p>
    <p class="text-slate-600">Chacun des six CUR a été érigé au rang d\'université autonome et indépendante. <strong>L\'Université de Mahajanga est née de cette décentralisation.</strong></p>
</div>

<div class="border-l-4 border-blue-500 pl-4 py-2">
    <p class="text-blue-600 font-semibold">2002</p>
    <p class="font-medium">Statut Actuel</p>
    <p class="text-slate-600">L\'Université est régie par le Décret 2002-565 du 04 juillet 2002, consolidant sa structure et son offre de formation diversifiée.</p>
</div>
</div>',
                'page_type' => 'historique',
                'order' => 2,
                'is_published' => true,
            ]
        );

        // Page 3: Nos établissements
        OrganizationPage::updateOrCreate(
            ['slug' => 'historique-etablissements'],
            [
                'title' => 'Nos Établissements',
                'content' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
    <h4 class="text-lg font-semibold text-blue-600 mb-3">🎓 Facultés</h4>
    <ul class="space-y-1 text-slate-600">
        <li>• Faculté des Sciences des Technologies et de l\'Environnement</li>
        <li>• Faculté de Médecine</li>
        <li>• École Normale Supérieure</li>
    </ul>
</div>

<div>
    <h4 class="text-lg font-semibold text-blue-600 mb-3">🏛️ Instituts</h4>
    <ul class="space-y-1 text-slate-600">
        <li>• Institut d\'Odonto-Stomatologie Tropicale de Madagascar</li>
        <li>• Institut Universitaire de Gestion et de Management</li>
        <li>• Institut Supérieur des Sciences et de Technologie</li>
        <li>• Institut Universitaire de Technologie et d\'Agronomie de Mahajanga</li>
        <li>• Institut des Lettres, Civilisations et Sciences Sociales</li>
    </ul>
</div>

<div>
    <h4 class="text-lg font-semibold text-blue-600 mb-3">🏫 Écoles</h4>
    <ul class="space-y-1 text-slate-600">
        <li>• École des Arts et Techniques en Prothèses dentaire</li>
        <li>• École de Droit et des Sciences Politiques</li>
        <li>• École de Tourisme</li>
        <li>• École des Langues Commerciales Internationales</li>
        <li>• École de Vétérinaire</li>
        <li>• École de Pharmacie</li>
    </ul>
</div>

<div>
    <h4 class="text-lg font-semibold text-blue-600 mb-3">📚 Écoles Doctorales</h4>
    <ul class="space-y-1 text-slate-600">
        <li>• École Doctorale Génie du Vivant et Modélisation</li>
        <li>• École Doctorale des Écosystèmes Naturels</li>
        <li>• École Doctorale Nutrition-Environnement-Santé</li>
    </ul>
</div>
</div>',
                'page_type' => 'historique',
                'order' => 3,
                'is_published' => true,
            ]
        );

        // Page 4: Nos filières
        OrganizationPage::updateOrCreate(
            ['slug' => 'historique-filieres'],
            [
                'title' => 'Nos Filières de Formation',
                'content' => '<p class="mb-4 text-slate-600">L\'Université de Mahajanga offre un large éventail de formations pluridisciplinaires :</p>
<div class="flex flex-wrap gap-2">
<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Finance et Comptabilité</span>
<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Gestion des Ressources Humaines</span>
<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Management</span>
<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Marketing et Communication</span>
<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Commerce International</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Génie Civil (BTP)</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Génie Hydraulique</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Génie Électrique</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Génie Informatique</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Génie Logiciel</span>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Télécommunication et Réseaux</span>
<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">Agriculture</span>
<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">Élevage</span>
<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">Environnement</span>
<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm">Pêche et Aquaculture</span>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Médecine humaine</span>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Infirmier généraliste</span>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Maïeutique</span>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Pharmacie</span>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Vétérinaire</span>
<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Biochimie</span>
<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Zoologie</span>
<span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Sciences de la Terre</span>
<span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Droit</span>
<span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Tourisme</span>
<span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">Langues Commerciales</span>
<span class="px-3 py-1 bg-slate-200 text-slate-700 rounded-full text-sm">et bien d\'autres...</span>
</div>',
                'page_type' => 'historique',
                'order' => 4,
                'is_published' => true,
            ]
        );

        $this->command->info('✅ Contenu historique créé avec succès !');
    }
}
