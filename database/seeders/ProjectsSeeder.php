<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        Project::updateOrCreate(
            ['slug' => 'infprev4frica'],
            [
                'kicker' => 'Projets Internationale',
                'title' => 'Projet InfPrev4frica',
                'description' => 'InovSafeCare Project Extension — prévention et contrôle des infections (PCI) et lutte contre les bactéries multi-résistantes (BMR).',
                'meta' => [
                    'tabs' => [
                        [
                            'key' => 'presentation',
                            'label' => 'Présentation',
                            'type' => 'richText',
                            'content_html' => '<h2>InfPrev4frica à Mahajanga</h2><p><strong>InovSafeCare Project Extension</strong></p><p>Il s\'agit d\'un projet de l\'Union Européenne (UE), basé sur le principe que la formation des professionnels de la santé doit prioriser les besoins sanitaires de la société.</p><p>L\'objectif est d\'améliorer la compétence des infirmiers en prévention et contrôle des infections nosocomiales (PCI) et face à l\'émergence des bactéries multi-résistantes (BMR).</p><p><a href="https://infprev4frica.eu/pt/home-pt" target="_blank" rel="noopener noreferrer">Site officiel du projet InfPrev4frica</a></p>',
                        ],
                        [
                            'key' => 'objectifs',
                            'label' => 'Objectifs',
                            'type' => 'richText',
                            'content_html' => '<h2>Objectifs du projet</h2><p>L\'objectif principal du projet est d\'améliorer la compétence des infirmiers en matière de prévention et de contrôle des infections nosocomiales (PCI), ainsi qu\'à faire face à l\'émergence des BMR dans les établissements de santé à Madagascar, notamment à Mahajanga.</p><p>Les enseignants peuvent jouer un rôle actif en renforçant les efforts de formation dans les universités et écoles des sciences de la santé.</p>',
                        ],
                        [
                            'key' => 'contexte',
                            'label' => 'Contexte',
                            'type' => 'richText',
                            'content_html' => '<h2>Contexte sanitaire</h2><p>L\'augmentation de la prévalence des infections causées par les bactéries multi-résistantes constitue un problème majeur, tant dans les établissements de santé que dans la communauté.</p><p>À Madagascar, l\'infection du site opératoire figure parmi les principales causes de morbi-mortalité. Le taux élevé de morbi-mortalité lié aux IAS et à l\'émergence des BMR fragilise la population.</p><p>Parmi les facteurs contribuant à ce fléau : la méconnaissance, voire l\'absence de connaissance, sur les précautions standards de PCI.</p>',
                        ],
                        [
                            'key' => 'impact',
                            'label' => 'Impact',
                            'type' => 'richText',
                            'content_html' => '<h2>Impact attendu</h2><p>Les revues scientifiques montrent que l\'application efficace des mesures de prévention et de lutte contre les infections nécessite une action continue à tous les niveaux du système de santé.</p><p>L\'enseignement supérieur figure parmi ces niveaux. Les enseignants peuvent jouer un rôle actif en contribuant à l\'élaboration de programmes de formation adaptés.</p>',
                        ],
                        [
                            'key' => 'photos',
                            'label' => 'Photos',
                            'type' => 'carousel',
                            'slides' => [
                                [
                                    'src' => '/images/placeholder.jpg',
                                    'alt' => 'Formation des professionnels',
                                    'title' => 'Formation des professionnels',
                                    'subtitle' => 'Institut de Formation Interrégionale des Paramédicaux',
                                ],
                                [
                                    'src' => '/images/placeholder.jpg',
                                    'alt' => 'Rencontre internationale',
                                    'title' => 'Rencontre internationale',
                                    'subtitle' => 'Réunion transnationale du projet ERASMUS+ InfPrev4frica',
                                ],
                                [
                                    'src' => '/images/placeholder.jpg',
                                    'alt' => 'Visite culturelle',
                                    'title' => 'Visite culturelle',
                                    'subtitle' => 'Participants du projet lors d\'une visite au baobab de Mahajanga',
                                ],
                            ],
                        ],
                        [
                            'key' => 'galerie',
                            'label' => 'Galerie',
                            'type' => 'gallery',
                            'images' => [
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Formation des professionnels', 'caption' => 'Institut des Paramédicaux'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Visite culturelle', 'caption' => 'Le baobab de Mahajanga'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Réunion ERASMUS+', 'caption' => 'Rencontre transnationale'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Consortium', 'caption' => 'Université de Mahajanga'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Experts internationaux', 'caption' => 'Collaboration scientifique'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Visite institution', 'caption' => 'École Supérieure Lisbonne'],
                            ],
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );

        Project::updateOrCreate(
            ['slug' => 'docet4africa'],
            [
                'kicker' => 'Projets Internationale',
                'title' => 'Projet DOCET4AFRICA',
                'description' => '« Doctorat Océan Indien : Coopération, Environnement et Training »',
                'meta' => [
                    'hero' => [
                        'badges' => [
                            ['variant' => 'primary', 'label' => 'Contract n°101083139'],
                            ['variant' => 'amber', 'label' => 'ERASMUS+ KA2 CBHE'],
                            ['variant' => 'emerald', 'label' => 'ERASMUS-EDU-20226CBHE'],
                        ],
                    ],
                    'tabs' => [
                        [
                            'key' => 'presentation',
                            'label' => 'Présentation',
                            'type' => 'richText',
                            'content_html' => '<h2>DOCET4AFRICA</h2><p>Projet de coopération académique pour le développement durable dans l’Océan Indien.</p><p>DOCET4AFRICA vise à renforcer la coopération Europe–Océan Indien, à moderniser l’enseignement supérieur au niveau Doctorat, et à promouvoir une gestion durable des ressources et de la biodiversité.</p>',
                        ],
                        [
                            'key' => 'partenaires',
                            'label' => 'Partenaires',
                            'type' => 'gallery',
                            'images' => [
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Union Européenne', 'caption' => 'Union Européenne'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Università di Torino', 'caption' => 'Università di Torino'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Université de La Réunion', 'caption' => 'Université de La Réunion'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Université des Comores', 'caption' => 'Université des Comores'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Université d’Antananarivo', 'caption' => 'Université d’Antananarivo'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Université de Mahajanga', 'caption' => 'Université de Mahajanga'],
                                ['src' => '/images/placeholder.jpg', 'alt' => 'Université de Toamasina', 'caption' => 'Université de Toamasina'],
                            ],
                        ],
                        [
                            'key' => 'apropos',
                            'label' => 'À propos',
                            'type' => 'richText',
                            'content_html' => '<h2>Objectif global</h2><p>Le projet DOCET4AFRICA contribue à favoriser la coopération Europe–Océan Indien à travers :</p><ul><li>La mise en réseau et l’échange d’expertise…</li><li>La mise en œuvre des plans stratégiques…</li><li>La création d’une offre éducative…</li></ul><h2>Objectifs spécifiques</h2><ul><li>Accroître la capacité…</li><li>Améliorer l’accès au marché du travail…</li><li>Promouvoir la sensibilisation…</li></ul><h2>Activités du projet</h2><ul><li>Renforcer les capacités…</li><li>Développer les programmes…</li><li>Sensibiliser et capitaliser…</li><li>Mettre en place une gestion…</li></ul><h2>Résultats attendus</h2><ul><li>Renforcement de la compétitivité…</li><li>Évolution de programmes…</li><li>Sensibilisation des communautés…</li><li>Suivi régulier et évaluation…</li></ul>',
                        ],
                        [
                            'key' => 'actualite',
                            'label' => 'Actualité',
                            'type' => 'richText',
                            'content_html' => '<h2>Publication d’Article</h2><p><strong>Troisième réunion du Comité de Pilotage (CDP) – DOCET4AFRICA</strong></p><p>Les 06 et 07 Août 2025…</p><p><a href="https://www.facebook.com/reel/3951572748443784" target="_blank" rel="noopener noreferrer">Voir la vidéo sur Facebook (Diffusion TVM)</a></p>',
                        ],
                        [
                            'key' => 'evenements',
                            'label' => 'Événements',
                            'type' => 'richText',
                            'content_html' => '<h2>Événements du projet</h2><ul><li><strong>31 Mai 2023</strong> — Accueil des représentants</li><li><strong>01 Juin 2023</strong> — Lancement officiel du projet</li><li><strong>06 Juin 2023</strong> — Signature de « Consortium Agreement »</li><li><strong>10 Juin 2024</strong> — Réunion des Comités de Pilotage (CDP)</li></ul>',
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );
    }
}
