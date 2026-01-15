<?php

namespace Database\Seeders;

use App\Models\OrganizationPage;
use Illuminate\Database\Seeder;

class OrganizationContentSeeder extends Seeder
{
    public function run(): void
    {
        $organisationContent = <<<'HTML'
<div class="org-card">
  <div class="org-card-header">
    <p class="org-kicker">UNIVERSITE DE MAHAJANGA</p>
  </div>
  <div class="org-card-body">
    <section class="org-block">
      <h3>Abreviations</h3>
      <ul>
        <li><strong>S.L.D.C.</strong> : Service Législation, Documentation et Contentieux</li>
        <li><strong>A.I.S.E.</strong> : Audit Interne et Suivi-Évaluation</li>
        <li><strong>P.R.M.P.</strong> : Personne Responsable des Marchés Publiques</li>
        <li><strong>DIR CAB</strong> : Directeur du Cabinet</li>
        <li><strong>D.R</strong> : Direction de Recherches</li>
        <li><strong>D.M.J</strong> : Direction des Musées et Jardin Botanique</li>
        <li><strong>D.P</strong> : Direction du Patrimoine</li>
        <li><strong>DAAF</strong> : Direction des Affaires Administratives et Financières</li>
        <li><strong>D.T.I.C</strong> : Direction des Technologies de l’Information et de la Communication</li>
        <li><strong>DOB</strong> : Direction de l’Office du Baccalauréat</li>
        <li><strong>DVU</strong> : Direction de la Vie Universitaire</li>
        <li><strong>D.F</strong> : Direction de la Formation</li>
        <li><strong>COUM</strong> : Centre des Œuvres Universitaires de Mahajanga</li>
        <li><strong>PAT</strong> : Personnel Administratif et Technique</li>
        <li><strong>PGI</strong> : Progiciel de Gestion Intégrée</li>
        <li><strong>FOAD</strong> : Formation Ouverte à Distance</li>
      </ul>
    </section>
    <section class="org-block">
      <h3>Établissements et Formations rattachés</h3>
      <ul>
        <li><strong>I.O.S.T.M.</strong> : Institut d’Odonto-Stomatologie Tropicale de Madagascar</li>
        <li><strong>F.S.T.E.</strong> : Faculté de Sciences, de Technologies et de l’Environnement</li>
        <li><strong>E.D.S.P.</strong> : École de Droit et de Science Politique</li>
        <li><strong>ILC-SS</strong> : Institut des Lettres, Civilisations et Sciences Sociales</li>
        <li><strong>E.T.</strong> : École de Tourisme</li>
        <li><strong>U.F.R.S.S</strong> : Unité de Formation et de Recherche en Sciences Sociales</li>
        <li><strong>E.L.C.I.</strong> : École de Langues Commerciales Internationales</li>
        <li><strong>I.S.S.T.M.</strong> : Institut Supérieur de Sciences et Technologies de Mahajanga</li>
        <li><strong>I.U.T.A.M.</strong> : Institut Universitaire de Technologies et d’Agronomie de Mahajanga</li>
        <li><strong>I.U.G.M.</strong> : Institut Universitaire de Gestion et Management</li>
        <li><strong>E.A.T.P.</strong> : École des Arts et Prothèse Dentaire</li>
      </ul>
    </section>
    <section class="org-block">
      <h3>Organisations</h3>
      <div class="org-pill-list">
        <span>Conseil d’Administration</span>
        <span>Président</span>
        <span>Conseil Scientifique</span>
      </div>
      <hr class="org-sep">
      <div class="org-pill-list">
        <span>Conseil des sages</span>
        <span>Comité d’éthique</span>
      </div>
    </section>
    <section class="org-block">
      <details class="org-accordion">
        <summary>Services rattachés à la présidence</summary>
        <ul>
          <li>Service de digitalisation et de bourse nationale et internationale</li>
          <li>Service de Législation, Documentation et Contentieux</li>
          <li>Service de l’Audit Interne et unité de Suivi-Évaluation</li>
          <li>Service anti-corruption et violence universitaire</li>
          <li>Personne Responsable des Marchés Publiques</li>
        </ul>
      </details>
      <details class="org-accordion">
        <summary>Cabinet de la présidence</summary>
        <ul>
          <li>Directeur du cabinet</li>
          <li>Conseillers</li>
          <li>Service sureté et prévention</li>
        </ul>
      </details>
      <details class="org-accordion">
        <summary>Premier Vice-Président</summary>
        <ul>
          <li>Direction de la vie universitaire</li>
          <ul>
            <li>Service des Sports, Arts et Culture</li>
            <li>Service de la Médecine Préventive et de la Promotion de la Santé (SMPPS)</li>
            <li>Service COUM</li>
            <li>Service Espace Vert</li>
            <li>Service sécurité</li>
          </ul>
          <li>Direction des Musées et Jardin Botanique</li>
          <ul>
            <li>Service Jardin botanique et centre Mandravasarotra Antsanitia</li>
            <li>Service Mozea Akiba</li>
            <li>Service Musée de la Mer</li>
            <li>Service Musée de l’Androna</li>
          </ul>
        </ul>
      </details>
      <details class="org-accordion">
        <summary>Deuxième Vice-Président</summary>
        <ul>
          <li>Direction de la Formation</li>
          <ul>
            <li>Service de formation et perfectionnement</li>
            <li>Service LMD</li>
            <li>Service scolarité centrale</li>
          </ul>
          <li>Direction de la Recherche</li>
          <ul>
            <li>Service d’Appui à la Recherche</li>
            <li>Service de partenariat et des relations internationales</li>
          </ul>
        </ul>
      </details>
      <details class="org-accordion">
        <summary>Directions rattachées au Président</summary>
        <ul>
          <li>Direction du Patrimoine</li>
          <ul>
            <li>Service Maintenance des Infrastructures et Logistique</li>
            <li>Service du Patrimoine</li>
          </ul>
          <li>Direction des Affaires Administratives et Financières</li>
          <ul>
            <li>Service Financier</li>
            <li>Service de la Gestion des Ressources Humaines</li>
            <li>Service de Suivi et Contrôle Interne</li>
            <li>Service Formation du PAT</li>
            <li>Services des Relations et Actions Sociales</li>
          </ul>
          <li>Direction des Technologies de l’Information et de la Communication</li>
          <ul>
            <li>Service de Maintenance Informatique</li>
            <li>Service d’administration réseau et Informatisation</li>
            <li>Communication Universitaire</li>
            <li>Service Radio Université Mahajanga</li>
            <li>Service de la Communication et Informatique en Ligne</li>
          </ul>
        </ul>
      </details>
      <details class="org-accordion">
        <summary>Direction de l’Office du Baccalauréat</summary>
        <ul>
          <li>Chargé de Coordination et de Contrôle</li>
          <li>Service Administratif et Logistique</li>
          <li>Service Technique et Informatique</li>
          <li>Service Financier</li>
        </ul>
      </details>
    </section>
  </div>
</div>
HTML;

        $organigrammeContent = <<<'HTML'
<div class="org-stack">
  <div class="org-card">
    <div class="org-card-header">
      <p class="org-kicker">PRESIDENCE DE L’UNIVERSITE DE MAHAJANGA</p>
    </div>
    <div class="org-card-body org-people">
      <div class="org-person">
        <div class="org-avatar-wrap">
          <img class="org-avatar" src="http://127.0.0.1:8001/storage/president/sZSkn7mxKvaG83Yw2edsW14el2QDC3i7Vb9lbi8c.png" alt="avatar">
          <img class="org-medal" src="http://127.0.0.1:8001/assets/images/element/medal-badge.png" alt="">
        </div>
        <div>
          <p class="org-person-name">Professeur Titulaire RANDRIANAMBININA Blanchard</p>
          <p class="org-person-role">Président</p>
        </div>
      </div>
      <div class="org-person">
        <div class="org-avatar-wrap">
          <img class="org-avatar" src="http://127.0.0.1:8001/assets/images/avatar/vice_president.jpg" alt="avatar">
          <img class="org-medal" src="http://127.0.0.1:8001/assets/images/element/medal-badge.png" alt="">
          <span class="org-rank">1<sup>ère</sup></span>
        </div>
        <div>
          <p class="org-person-name">Dr. RAKOTOARIVELO Geoslin</p>
          <p class="org-person-role">Vice Président I</p>
        </div>
      </div>
      <div class="org-person">
        <div class="org-avatar-wrap">
          <img class="org-avatar" src="http://127.0.0.1:8001/assets/images/avatar/pr_mahefa.jpg" alt="avatar">
          <img class="org-medal" src="http://127.0.0.1:8001/assets/images/element/medal-badge.png" alt="">
          <span class="org-rank">2<sup>ème</sup></span>
        </div>
        <div>
          <p class="org-person-name">Pr Titulaire RAZAFIMAHEFA</p>
          <p class="org-person-role">Vice Président II</p>
        </div>
      </div>
    </div>
  </div>
  <figure class="org-card">
    <div class="org-card-header">
      <p class="org-kicker">ORGANIGRAMME VISUEL</p>
    </div>
    <div class="org-card-body">
      <img class="org-image" src="http://127.0.0.1:8001/storage/orga/cuC6QctO4NrCIvQOgBj4GDfQ2ZcUM9U4QrPMqp0s.jpg" alt="Organigramme de l'Université de Mahajanga">
    </div>
  </figure>
</div>
HTML;

        OrganizationPage::updateOrCreate(
            ['slug' => 'organisation'],
            [
                'title' => 'Organisation',
                'content' => $organisationContent,
                'page_type' => 'organisation',
                'order' => 1,
                'is_published' => true,
                'meta_title' => 'Organisation - Université de Mahajanga',
                'meta_description' => 'Organisation, présidence, directions et services de l\'Université de Mahajanga.',
            ]
        );

        OrganizationPage::updateOrCreate(
            ['slug' => 'organigramme'],
            [
                'title' => 'Organigramme',
                'content' => $organigrammeContent,
                'page_type' => 'organigramme',
                'order' => 1,
                'is_published' => true,
                'meta_title' => 'Organigramme - Université de Mahajanga',
                'meta_description' => 'Organigramme et présidence de l\'Université de Mahajanga.',
            ]
        );

        $this->command->info('✅ Contenu organisation créé avec succès !');
    }
}
