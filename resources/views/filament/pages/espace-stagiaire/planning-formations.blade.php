{{-- ESPACE_STAGIAIRE_PLANNING_FILAMENT_V1 --}}
<x-filament-panels::page>

    <style>
.ps-stagiaire-planning, .ps-stagiaire-planning *{
            box-sizing: border-box;
        }.ps-stagiaire-planning{
            margin: 0;
            padding: 2rem 1rem;
            background: #f1f5f9;
            color: #0f172a;
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }.ps-stagiaire-planning .container{
            max-width: 1450px;
            margin: 0 auto;
        }.ps-stagiaire-planning .header{
            margin-bottom: 1.5rem;
        }.ps-stagiaire-planning .header-top{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }.ps-stagiaire-planning h1{
            margin: 0 0 .4rem;
            font-size: 2rem;
        }.ps-stagiaire-planning .subtitle{
            color: #64748b;
        }.ps-stagiaire-planning .header-actions{
            display: flex;
            gap: .65rem;
            flex-wrap: wrap;
        }.ps-stagiaire-planning .need-button, .ps-stagiaire-planning .follow-button{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            border-radius: .65rem;
            color: white;
            text-decoration: none;
            font-weight: 750;
        }.ps-stagiaire-planning .need-button{
            background: #0f766e;
        }.ps-stagiaire-planning .need-button:hover{
            background: #115e59;
        }.ps-stagiaire-planning .follow-button{
            background: #475569;
        }.ps-stagiaire-planning .follow-button:hover{
            background: #334155;
        }.ps-stagiaire-planning .flash-success{
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #86efac;
            border-radius: .8rem;
            background: #f0fdf4;
            color: #166534;
        }.ps-stagiaire-planning .flash-icon{
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1;
        }.ps-stagiaire-planning .flash-title{
            font-weight: 800;
            margin-bottom: .2rem;
        }.ps-stagiaire-planning .flash-reference{
            font-size: .9rem;
        }.ps-stagiaire-planning .portal-info{
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }.ps-stagiaire-planning .portal-card{
            padding: 1rem 1.2rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .8rem;
        }.ps-stagiaire-planning .portal-card strong{
            display: block;
            margin-bottom: .3rem;
        }.ps-stagiaire-planning .portal-card span{
            color: #64748b;
            font-size: .9rem;
            line-height: 1.45;
        }.ps-stagiaire-planning .toolbar{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }.ps-stagiaire-planning .toolbar-group{
            display: flex;
            gap: .5rem;
            align-items: center;
        }.ps-stagiaire-planning .month-title{
            min-width: 220px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 750;
        }.ps-stagiaire-planning .button{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .6rem .9rem;
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: white;
            color: #0f172a;
            font-weight: 650;
            text-decoration: none;
        }.ps-stagiaire-planning .button:hover{
            background: #f8fafc;
        }.ps-stagiaire-planning .calendar-scroll{
            overflow-x: auto;
        }.ps-stagiaire-planning .calendar{
            min-width: 1150px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .9rem;
            overflow: hidden;
            box-shadow:
                0 2px 8px
                rgba(15, 23, 42, .04);
        }.ps-stagiaire-planning .week-header{
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }.ps-stagiaire-planning .week-header div{
            padding: .75rem;
            text-align: center;
            font-weight: 750;
            color: #475569;
        }.ps-stagiaire-planning .week-row{
            display: grid;
            grid-template-columns:
                58px
                repeat(5, minmax(0, 1fr));
        }.ps-stagiaire-planning .week-number{
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-weight: 750;
        }.ps-stagiaire-planning .week-number span{
            padding: .35rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: .45rem;
        }.ps-stagiaire-planning .day{
            min-height: 180px;
            padding: .5rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }.ps-stagiaire-planning .week-row .day:last-child{
            border-right: none;
        }.ps-stagiaire-planning .day.outside{
            background: #f8fafc;
        }.ps-stagiaire-planning .day-number{
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            font-weight: 750;
            margin-bottom: .5rem;
        }.ps-stagiaire-planning .outside .day-number{
            color: #94a3b8;
        }.ps-stagiaire-planning .day-number.today{
            background: #2563eb;
            color: white;
        }.ps-stagiaire-planning .session{
            padding: .65rem;
            margin-bottom: .5rem;
            border-radius: .6rem;
            background:
                var(
                    --stage-bg,
                    #eff6ff
                );
            border-left:
                4px solid
                var(
                    --stage-border,
                    #2563eb
                );
        }.ps-stagiaire-planning .session-title{
            margin-bottom: .25rem;
            font-weight: 800;
            color:
                var(
                    --stage-text,
                    #1e3a8a
                );
        }.ps-stagiaire-planning .places{
            margin-top: .35rem;
            font-size: .78rem;
            font-weight: 700;
        }.ps-stagiaire-planning .places.available{
            color: #15803d;
        }.ps-stagiaire-planning .places.full{
            color: #c2410c;
        }.ps-stagiaire-planning .register{
            display: inline-flex;
            margin-top: .5rem;
            padding: .42rem .65rem;
            border-radius: .5rem;
            background:
                var(
                    --stage-border,
                    #2563eb
                );
            color: white;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 750;
        }.ps-stagiaire-planning .register:hover{
            background:
                var(
                    --stage-button-hover,
                    #1d4ed8
                );
        }.ps-stagiaire-planning .empty{
            margin-top: .5rem;
            font-size: .75rem;
            color: #94a3b8;
        }.ps-stagiaire-planning .legend{
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            color: #64748b;
            font-size: .85rem;
        }.ps-stagiaire-planning .legend-item{
            display: flex;
            align-items: center;
            gap: .4rem;
        }.ps-stagiaire-planning .legend-dot{
            width: .8rem;
            height: .8rem;
            border-radius: 9999px;
        }.ps-stagiaire-planning .dot-stage{
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #16a34a,
                    #9333ea,
                    #ea580c
                );
        }.ps-stagiaire-planning .dot-full{
            background: #f59e0b;
        }

        @media (max-width: 900px) {.ps-stagiaire-planning .portal-info{
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {.ps-stagiaire-planning{
                padding: 1rem .6rem;
            }.ps-stagiaire-planning h1{
                font-size: 1.55rem;
            }.ps-stagiaire-planning .header-actions{
                width: 100%;
            }.ps-stagiaire-planning .need-button, .ps-stagiaire-planning .follow-button{
                flex: 1;
            }
        }/* PDF_CANDIDATURE_POPUP_V1 */
.ps-stagiaire-planning .candidature-pdf-box{
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-wrap: wrap;
            margin-top: .9rem;
            padding: .8rem;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
        }.ps-stagiaire-planning .candidature-pdf-icon{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.7rem;
            height: 2.2rem;
            padding: 0 .4rem;
            border-radius: .4rem;
            background: #dc2626;
            color: white;
            font-size: .72rem;
            font-weight: 800;
        }.ps-stagiaire-planning .candidature-pdf-content{
            display: flex;
            flex: 1 1 220px;
            flex-direction: column;
            gap: .15rem;
        }.ps-stagiaire-planning .candidature-pdf-content span{
            color: #475569;
            font-size: .85rem;
        }.ps-stagiaire-planning .candidature-pdf-button{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .62rem .85rem;
            border-radius: .55rem;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: .86rem;
            font-weight: 750;
        }.ps-stagiaire-planning .candidature-pdf-button:hover{
            background: #1d4ed8;
        }/* PORTAIL_LIBELLE_LONG_SURVOL_V1_1 */
.ps-stagiaire-planning .session{
            position: relative;
        }.ps-stagiaire-planning .session:hover{
            z-index: 50;
        }.ps-stagiaire-planning .stage-long-tooltip{
            display: none;
            position: absolute;
            left: 0;
            top: calc(100% + .45rem);
            z-index: 1000;
            min-width: 230px;
            width: max-content;
            max-width: 380px;
            padding: .7rem .8rem;
            border-radius: .6rem;
            background: #0f172a;
            color: white;
            box-shadow:
                0 10px 25px
                rgba(15, 23, 42, .22);
            font-size: .82rem;
            font-weight: 600;
            line-height: 1.4;
            white-space: normal;
            pointer-events: none;
        }.ps-stagiaire-planning .session:hover .stage-long-tooltip{
            display: block;
        }.ps-stagiaire-planning .stage-long-tooltip::before{
            content: "";
            position: absolute;
            left: 1rem;
            bottom: 100%;
            border-left: .35rem solid transparent;
            border-right: .35rem solid transparent;
            border-bottom: .35rem solid #0f172a;
        }

        @media (max-width: 700px) {.ps-stagiaire-planning .stage-long-tooltip{
                max-width: 280px;
            }
        }/* PORTAIL_VUES_RECHERCHE_STAGES_V1_3 */
.ps-stagiaire-planning .portal-tools{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }.ps-stagiaire-planning .stage-search{
            display: flex;
            flex: 1 1 560px;
            align-items: center;
            gap: .55rem;
        }.ps-stagiaire-planning .stage-search input[type="search"]{
            flex: 1 1 auto;
            min-width: 210px;
            padding: .72rem .85rem;
            border: 1px solid #cbd5e1;
            border-radius: .65rem;
            background: white;
            color: #0f172a;
            font: inherit;
        }.ps-stagiaire-planning .stage-search input[type="search"]:focus{
            border-color: #3b82f6;
            outline: 2px solid #bfdbfe;
            outline-offset: 1px;
        }.ps-stagiaire-planning .search-button, .ps-stagiaire-planning .clear-search, .ps-stagiaire-planning .view-switch{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            min-height: 42px;
            padding: .65rem .85rem;
            border: 1px solid #cbd5e1;
            border-radius: .65rem;
            background: white;
            color: #334155;
            text-decoration: none;
            font-weight: 750;
            cursor: pointer;
        }.ps-stagiaire-planning .search-button{
            border-color: #2563eb;
            background: #2563eb;
            color: white;
        }.ps-stagiaire-planning .search-button:hover{
            background: #1d4ed8;
        }.ps-stagiaire-planning .clear-search:hover, .ps-stagiaire-planning .view-switch:hover{
            background: #f8fafc;
        }.ps-stagiaire-planning .view-switcher{
            display: inline-flex;
            gap: .4rem;
        }.ps-stagiaire-planning .view-switch.active{
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }.ps-stagiaire-planning .view-switch svg{
            width: 19px;
            height: 19px;
            flex: 0 0 auto;
        }.ps-stagiaire-planning .search-summary{
            flex-basis: 100%;
            padding: .7rem .85rem;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: .9rem;
        }.ps-stagiaire-planning .search-summary a{
            margin-left: .35rem;
            color: #1d4ed8;
            font-weight: 800;
        }.ps-stagiaire-planning .calendar-view-hidden{
            display: none !important;
        }.ps-stagiaire-planning .list-view-container{
            max-width: 1450px;
            margin: 1rem auto 0;
        }.ps-stagiaire-planning .sessions-list{
            display: flex;
            flex-direction: column;
            gap: 1.35rem;
        }.ps-stagiaire-planning .sessions-date-group{
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }.ps-stagiaire-planning .sessions-date-title{
            padding-bottom: .45rem;
            border-bottom: 1px solid #cbd5e1;
            color: #334155;
            font-size: 1.02rem;
            font-weight: 800;
        }.ps-stagiaire-planning .list-session-card{
            display: grid;
            grid-template-columns:
                minmax(0, 1fr) auto;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            border-radius: .75rem;
            background: white;
        }.ps-stagiaire-planning .list-session-card.full{
            border-left-color: #f59e0b;
        }.ps-stagiaire-planning .list-session-title{
            color: #1e3a8a;
            font-size: 1.05rem;
            font-weight: 800;
        }.ps-stagiaire-planning .list-session-long{
            margin-top: .25rem;
            color: #475569;
            font-size: .9rem;
            line-height: 1.4;
        }.ps-stagiaire-planning .list-session-meta{
            display: flex;
            gap: .45rem 1rem;
            flex-wrap: wrap;
            margin-top: .65rem;
            color: #475569;
            font-size: .83rem;
        }.ps-stagiaire-planning .list-session-actions{
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: .55rem;
            min-width: 180px;
        }.ps-stagiaire-planning .list-places{
            font-size: .85rem;
            font-weight: 800;
        }.ps-stagiaire-planning .list-places.available{
            color: #15803d;
        }.ps-stagiaire-planning .list-places.full{
            color: #c2410c;
        }.ps-stagiaire-planning .list-empty{
            padding: 2rem 1rem;
            border: 1px dashed #cbd5e1;
            border-radius: .75rem;
            background: white;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 760px) {.ps-stagiaire-planning .stage-search{
                flex-basis: 100%;
                flex-wrap: wrap;
            }.ps-stagiaire-planning .stage-search input[type="search"]{
                flex-basis: 100%;
            }.ps-stagiaire-planning .view-switcher{
                width: 100%;
            }.ps-stagiaire-planning .view-switch{
                flex: 1;
            }.ps-stagiaire-planning .list-session-card{
                grid-template-columns: 1fr;
            }.ps-stagiaire-planning .list-session-actions{
                align-items: stretch;
                min-width: 0;
            }
        }/* CALENDRIER_ALIGNEMENT_STAGES_V1 */
.ps-stagiaire-planning .session-slot{
            margin-bottom: .5rem;
        }.ps-stagiaire-planning .session-slot > .session{
            height: 100%;
            margin-bottom: 0;
        }.ps-stagiaire-planning .session-slot-empty{
            visibility: hidden;
            pointer-events: none;
        }/* PORTAIL_VUE_SEMAINE_V1 */
.ps-stagiaire-planning .week-view-container{
            max-width: 1450px;
            margin: 1rem auto 0;
        }.ps-stagiaire-planning .week-toolbar{
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: .8rem;
            margin-bottom: 1rem;
        }.ps-stagiaire-planning .week-toolbar-center{
            text-align: center;
        }.ps-stagiaire-planning .week-title{
            font-size: 1.18rem;
            font-weight: 800;
            color: #0f172a;
        }.ps-stagiaire-planning .week-toolbar-actions{
            display: flex;
            gap: .45rem;
            align-items: center;
        }.ps-stagiaire-planning .week-calendar-scroll{
            overflow-x: auto;
        }.ps-stagiaire-planning .week-calendar{
            min-width: 1000px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: .9rem;
            background: white;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }.ps-stagiaire-planning .week-days-header, .ps-stagiaire-planning .week-events-grid{
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }.ps-stagiaire-planning .week-day-header{
            padding: .8rem .7rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
        }.ps-stagiaire-planning .week-day-header:last-child{
            border-right: 0;
        }.ps-stagiaire-planning .week-day-name{
            display: block;
            color: #475569;
            font-size: .82rem;
            font-weight: 750;
            text-transform: capitalize;
        }.ps-stagiaire-planning .week-day-number{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.1rem;
            height: 2.1rem;
            margin-top: .25rem;
            padding: 0 .4rem;
            border-radius: 9999px;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 850;
        }.ps-stagiaire-planning .week-day-number.today{
            background: #2563eb;
            color: white;
        }.ps-stagiaire-planning .week-cell{
            min-height: 118px;
            padding: .5rem;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }.ps-stagiaire-planning .week-events-grid .week-cell:nth-child(5n){
            border-right: 0;
        }.ps-stagiaire-planning .week-cell .session{
            height: 100%;
            min-height: 96px;
            margin-bottom: 0;
        }.ps-stagiaire-planning .week-empty-lane{
            min-height: 96px;
        }.ps-stagiaire-planning .week-no-session{
            grid-column: 1 / -1;
            padding: 2rem 1rem;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 760px) {.ps-stagiaire-planning .week-toolbar{
                grid-template-columns: 1fr;
            }.ps-stagiaire-planning .week-toolbar-center{
                order: -1;
            }.ps-stagiaire-planning .week-toolbar-actions{
                justify-content: center;
                flex-wrap: wrap;
            }
        }/*
         * Couche VISUELLE uniquement.
         * Aucun HTML, aucune variable Blade et aucune logique métier
         * de la page existante ne sont modifiés.
         */
.ps-stagiaire-planning{
            --skeletor-primary: #2563eb;
            --skeletor-primary-hover: #1d4ed8;
            --skeletor-bg: #f9fafb;
            --skeletor-card: #ffffff;
            --skeletor-border: #e5e7eb;
            --skeletor-border-strong: #d1d5db;
            --skeletor-text: #111827;
            --skeletor-muted: #6b7280;
            --skeletor-subtle: #f3f4f6;
            --skeletor-success: #15803d;
            --skeletor-warning: #c2410c;
            --skeletor-radius: .75rem;
            --skeletor-shadow:
                0 1px 2px rgba(0, 0, 0, .04),
                0 1px 3px rgba(0, 0, 0, .08);
        }.ps-stagiaire-planning{
            background: var(--skeletor-bg);
        }.ps-stagiaire-planning{
            background: var(--skeletor-bg);
            color: var(--skeletor-text);
        }.ps-stagiaire-planning .container{
            max-width: 1450px;
        }.ps-stagiaire-planning .header{
            margin-bottom: 1.5rem;
        }.ps-stagiaire-planning h1{
            margin: 0 0 .25rem;
            color: var(--skeletor-text);
            font-size: 1.875rem;
            line-height: 2.25rem;
            font-weight: 700;
            letter-spacing: -.025em;
        }.ps-stagiaire-planning .subtitle{
            color: var(--skeletor-muted);
            font-size: .925rem;
        }.ps-stagiaire-planning .header-actions{
            gap: .5rem;
        }.ps-stagiaire-planning .need-button, .ps-stagiaire-planning .follow-button, .ps-stagiaire-planning .button, .ps-stagiaire-planning .search-button, .ps-stagiaire-planning .clear-search, .ps-stagiaire-planning .candidature-pdf-button, .ps-stagiaire-planning .register{
            border-radius: .5rem;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            transition:
                background-color .15s ease,
                border-color .15s ease,
                color .15s ease;
        }.ps-stagiaire-planning .need-button, .ps-stagiaire-planning .search-button{
            border: 1px solid var(--skeletor-primary);
            background: var(--skeletor-primary);
            color: #fff;
        }.ps-stagiaire-planning .need-button:hover, .ps-stagiaire-planning .search-button:hover{
            border-color: var(--skeletor-primary-hover);
            background: var(--skeletor-primary-hover);
        }.ps-stagiaire-planning .follow-button, .ps-stagiaire-planning .button, .ps-stagiaire-planning .clear-search{
            border: 1px solid var(--skeletor-border-strong);
            background: var(--skeletor-card);
            color: #374151;
        }.ps-stagiaire-planning .follow-button:hover, .ps-stagiaire-planning .button:hover, .ps-stagiaire-planning .clear-search:hover{
            background: var(--skeletor-subtle);
            color: var(--skeletor-text);
        }.ps-stagiaire-planning .portal-info{
            gap: 1rem;
        }.ps-stagiaire-planning .portal-card, .ps-stagiaire-planning .portal-tools, .ps-stagiaire-planning .calendar, .ps-stagiaire-planning .week-calendar, .ps-stagiaire-planning .sessions-date-group{
            border: 1px solid var(--skeletor-border);
            border-radius: var(--skeletor-radius);
            background: var(--skeletor-card);
            box-shadow: var(--skeletor-shadow);
        }.ps-stagiaire-planning .portal-card{
            padding: 1rem 1.1rem;
        }.ps-stagiaire-planning .portal-card strong{
            color: #1f2937;
            font-weight: 650;
        }.ps-stagiaire-planning .portal-card span{
            color: var(--skeletor-muted);
            font-size: .875rem;
        }.ps-stagiaire-planning .portal-tools{
            padding: 1rem;
        }.ps-stagiaire-planning .stage-search input[type="search"]{
            height: 2.5rem;
            border: 1px solid var(--skeletor-border-strong);
            border-radius: .5rem;
            background: #fff;
            color: var(--skeletor-text);
            font-size: .875rem;
            outline: none;
        }.ps-stagiaire-planning .stage-search input[type="search"]:focus{
            border-color: var(--skeletor-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }.ps-stagiaire-planning .view-switcher{
            border: 1px solid var(--skeletor-border);
            border-radius: .65rem;
            background: var(--skeletor-subtle);
        }.ps-stagiaire-planning .view-switch{
            border-radius: .45rem;
            color: #4b5563;
            font-weight: 600;
        }.ps-stagiaire-planning .view-switch:hover{
            color: var(--skeletor-text);
        }.ps-stagiaire-planning .view-switch.active{
            background: #fff;
            color: var(--skeletor-primary);
            box-shadow: var(--skeletor-shadow);
        }.ps-stagiaire-planning .search-summary{
            border: 1px solid #bfdbfe;
            border-radius: .6rem;
            background: #eff6ff;
            color: #1e3a8a;
        }.ps-stagiaire-planning .toolbar, .ps-stagiaire-planning .week-toolbar{
            margin-bottom: 1rem;
        }.ps-stagiaire-planning .month-title, .ps-stagiaire-planning .week-title{
            color: #1f2937;
            font-weight: 700;
        }.ps-stagiaire-planning .calendar, .ps-stagiaire-planning .week-calendar{
            overflow: hidden;
        }.ps-stagiaire-planning .week-header, .ps-stagiaire-planning .week-day-header, .ps-stagiaire-planning .week-number, .ps-stagiaire-planning .sessions-date-title{
            background: #f9fafb;
        }.ps-stagiaire-planning .week-header, .ps-stagiaire-planning .week-row, .ps-stagiaire-planning .week-number, .ps-stagiaire-planning .day, .ps-stagiaire-planning .week-day-header, .ps-stagiaire-planning .week-cell, .ps-stagiaire-planning .sessions-date-title, .ps-stagiaire-planning .list-session-card{
            border-color: var(--skeletor-border);
        }.ps-stagiaire-planning .day-number.today, .ps-stagiaire-planning .week-day-number.today{
            background: var(--skeletor-primary);
            color: #fff;
        }.ps-stagiaire-planning .places.available, .ps-stagiaire-planning .list-places.available{
            color: var(--skeletor-success);
        }.ps-stagiaire-planning .places.full, .ps-stagiaire-planning .list-places.full{
            color: var(--skeletor-warning);
        }.ps-stagiaire-planning .flash-success{
            border-color: #bbf7d0;
            border-radius: var(--skeletor-radius);
            background: #f0fdf4;
            box-shadow: var(--skeletor-shadow);
        }.ps-stagiaire-planning .candidature-pdf-box{
            border-radius: .6rem;
        }.ps-stagiaire-planning .sessions-date-group{
            overflow: hidden;
        }.ps-stagiaire-planning .list-session-card.full{
            background: #fffbeb;
        }

        @media (max-width: 768px) {.ps-stagiaire-planning{
                padding: 1rem .75rem 2rem;
            }.ps-stagiaire-planning h1{
                font-size: 1.5rem;
                line-height: 2rem;
            }
        }

    </style>

    <div class="ps-stagiaire-planning">
{{-- PORTAIL_URLS_RELATIVES_V1 --}}
@php
    $publicRoute =
        static function (
            string $name,
            array $parameters = []
        ): string {
            if (
                $name
                === 'fpsplanificationstage.public.calendrier'
            ) {
                $query =
                    http_build_query(
                        $parameters
                    );

                $base =
                    request()->url();

                return $query === ''
                    ? $base
                    : $base . '?' . $query;
            }

            return route(
                $name,
                $parameters,
                false
            );
        };
@endphp


<div class="container">

    <div class="header">

        <div class="header-top">

            <div>

                <h1>
                    Planning des formations
                </h1>

                <div class="subtitle">
                    Consultez les sessions,
                    inscrivez-vous ou transmettez
                    un besoin de formation.
                </div>

            </div>

            <div class="header-actions">

                <a
                    class="need-button"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.besoin.create'
                    ) }}"
                >
                    Exprimer un besoin en stage
                </a>

                <a
                    class="follow-button"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.besoin.suivi.form'
                    ) }}"
                >
                    Suivre un besoin
                </a>

            </div>

        </div>

    </div>

    @if (session('inscription_success'))

        <div class="flash-success">

            <div class="flash-icon">
                ✓
            </div>

            <div>

                <div class="flash-title">
                    {{ session('inscription_success') }}
                </div>

                @if (session('inscription_code'))

                    <div class="flash-reference">
                        Référence :
                        <strong>
                            {{ session('inscription_code') }}
                        </strong>
                    </div>


                    {{-- PDF_CANDIDATURE_POPUP_V1 --}}
                    @if (session('inscription_pdf_url'))

                        <div class="candidature-pdf-box">

                            <div class="candidature-pdf-icon">
                                PDF
                            </div>

                            <div class="candidature-pdf-content">

                                <strong>
                                    Fiche de candidature
                                </strong>

                                <span>
                                    Conservez le PDF récapitulatif de votre candidature.
                                </span>

                            </div>

                            <a
                                class="candidature-pdf-button"
                                href="{{ session('inscription_pdf_url') }}"
                            >
                                Télécharger le PDF
                            </a>

                        </div>

                    @endif
@endif

            </div>

        </div>

    @endif

    <div class="portal-info">

        <div class="portal-card">

            <strong>
                Vous souhaitez vous inscrire ?
            </strong>

            <span>
                Choisissez directement une session
                disponible dans le calendrier ci-dessous.
            </span>

        </div>

        <div class="portal-card">

            <strong>
                Aucune session ne correspond à votre besoin ?
            </strong>

            <span>
                Votre bâtiment ou votre unité peut transmettre
                directement une expression de besoin.
            </span>

        </div>

        <div class="portal-card">

            <strong>
                Vous avez déjà exprimé un besoin ?
            </strong>

            <span>
                Utilisez votre référence BES-xxxxxx
                et votre adresse e-mail pour suivre
                son avancement.
            </span>

        </div>

    </div>

        {{-- PORTAIL_VUES_RECHERCHE_STAGES_V1_3 --}}
    <div class="portal-tools">

        <form
            class="stage-search"
            method="GET"
            action="{{ $publicRoute(
                'fpsplanificationstage.public.calendrier'
            ) }}"
        >
            <input
                type="hidden"
                name="vue"
                value="{{ $viewMode }}"
            >

            @if ($viewMode === 'calendrier')
                <input
                    type="hidden"
                    name="mois"
                    value="{{ $moisCourant }}"
                >
            @endif

            @if ($viewMode === 'semaine')
                <input
                    type="hidden"
                    name="semaine"
                    value="{{ $semaineCourante }}"
                >
            @endif

            <input
                type="search"
                name="q"
                value="{{ $searchTerm }}"
                placeholder="Rechercher : libellé, FPS, service responsable…"
                aria-label="Rechercher un stage"
            >

            <button
                class="search-button"
                type="submit"
            >
                Rechercher
            </button>

            @if ($searchTerm !== '')
                <a
                    class="clear-search"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.calendrier',
                        array_filter([
                            'vue' => $viewMode,
                            'mois' =>
                                $viewMode === 'calendrier'
                                    ? $moisCourant
                                    : null,
                        ])
                    ) }}"
                >
                    Effacer
                </a>
            @endif
        </form>

        <div
            class="view-switcher"
            aria-label="Choisir la vue"
        >
            <a
                class="
                    view-switch
                    {{ $viewMode === 'calendrier' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'vue' => 'calendrier',
                        'mois' => $moisCourant,
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue calendrier"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
                Calendrier
            </a>

                        {{-- PORTAIL_VUE_SEMAINE_V1 --}}
            <a
                class="
                    view-switch
                    {{ $viewMode === 'semaine' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'vue' => 'semaine',
                        'semaine' => $semaineCourante,
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue semaine"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                    <path d="M7 14h2M11 14h2M15 14h2M7 18h2M11 18h2" />
                </svg>
                Semaine
            </a>
<a
                class="
                    view-switch
                    {{ $viewMode === 'liste' ? 'active' : '' }}
                "
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'vue' => 'liste',
                        'q' =>
                            $searchTerm !== ''
                                ? $searchTerm
                                : null,
                    ])
                ) }}"
                title="Vue liste"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="M8 6h13M8 12h13M8 18h13" />
                    <path d="M3 6h.01M3 12h.01M3 18h.01" />
                </svg>
                Liste
            </a>
        </div>

        @if ($searchTerm !== '')
            <div class="search-summary">
                Recherche :
                <strong>
                    « {{ $searchTerm }} »
                </strong>
                —
                {{ $matchingSessionsCount }}
                session(s) programmée(s) à venir.

                @if (
                    $viewMode === 'calendrier'
                    && $matchingSessionsCount > 0
                )
                    <a
                        href="{{ $publicRoute(
                            'fpsplanificationstage.public.calendrier',
                            [
                                'vue' => 'liste',
                                'q' => $searchTerm,
                            ]
                        ) }}"
                    >
                        Afficher toutes les sessions
                    </a>
                @endif
            </div>
        @endif

    </div>
<div class="toolbar {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="toolbar-group">

            <a
                class="button"
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'mois' => $moisPrecedent,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                ←
            </a>

        </div>

        <div class="month-title">
            {{ $moisLabel }}
        </div>

        <div class="toolbar-group">

            <a
                class="button"
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'mois' => $moisActuel,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                Aujourd’hui
            </a>

            <a
                class="button"
                href="{{ $publicRoute(
                    'fpsplanificationstage.public.calendrier',
                    array_filter([
                        'mois' => $moisSuivant,
                        'vue' => 'calendrier',
                        'q' => $searchTerm !== '' ? $searchTerm : null,
                    ])
                ) }}"
            >
                →
            </a>

        </div>

    </div>

    <div class="calendar-scroll {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="calendar">

            <div class="week-header">

                <div>Sem.</div>

                <div>Lun</div>
                <div>Mar</div>
                <div>Mer</div>
                <div>Jeu</div>
                <div>Ven</div>

            </div>

            @foreach (
                array_chunk(
                    array_values(
                        array_filter(
                            $days,
                            fn ($day) =>
                                \Carbon\Carbon::parse(
                                    $day['date']
                                )->isWeekday()
                        )
                    ),
                    5
                )
                as $week
            )


                {{-- CALENDRIER_ALIGNEMENT_STAGES_V1 --}}
                @php
                    /*
                     * Une session garde une ligne fixe pendant toute
                     * la semaine. Les sessions les plus longues sont
                     * placées en premier afin d'éviter l'effet escalier.
                     */
                    $calendarWeekSessions = [];

                    foreach ($week as $calendarDayIndex => $calendarDay) {
                        foreach (($calendarDay['events'] ?? []) as $calendarEvent) {
                            $calendarEventKey = (string) (
                                $calendarEvent['id']
                                ?? $calendarEvent['code']
                                ?? md5(json_encode($calendarEvent))
                            );

                            if (! isset($calendarWeekSessions[$calendarEventKey])) {
                                $calendarWeekSessions[$calendarEventKey] = [
                                    'event' => $calendarEvent,
                                    'first_day' => $calendarDayIndex,
                                    'last_day' => $calendarDayIndex,
                                    'days' => [],
                                    'stage' => (string) ($calendarEvent['stage'] ?? ''),
                                ];
                            }

                            $calendarWeekSessions[$calendarEventKey]['first_day'] = min(
                                $calendarWeekSessions[$calendarEventKey]['first_day'],
                                $calendarDayIndex
                            );

                            $calendarWeekSessions[$calendarEventKey]['last_day'] = max(
                                $calendarWeekSessions[$calendarEventKey]['last_day'],
                                $calendarDayIndex
                            );

                            $calendarWeekSessions[$calendarEventKey]['days'][$calendarDayIndex] = true;
                        }
                    }

                    uasort(
                        $calendarWeekSessions,
                        static function (array $left, array $right): int {
                            $leftDuration = count($left['days']);
                            $rightDuration = count($right['days']);

                            if ($leftDuration !== $rightDuration) {
                                return $rightDuration <=> $leftDuration;
                            }

                            if ($left['first_day'] !== $right['first_day']) {
                                return $left['first_day'] <=> $right['first_day'];
                            }

                            $stageCompare = strcasecmp(
                                $left['stage'],
                                $right['stage']
                            );

                            if ($stageCompare !== 0) {
                                return $stageCompare;
                            }

                            return 0;
                        }
                    );

                    $calendarLaneDays = [];
                    $calendarEventLanes = [];

                    foreach ($calendarWeekSessions as $calendarEventKey => $calendarMeta) {
                        $calendarLane = 0;

                        while (true) {
                            $calendarConflict = false;

                            foreach (array_keys($calendarMeta['days']) as $calendarOccupiedDay) {
                                if (isset($calendarLaneDays[$calendarLane][$calendarOccupiedDay])) {
                                    $calendarConflict = true;
                                    break;
                                }
                            }

                            if (! $calendarConflict) {
                                break;
                            }

                            $calendarLane++;
                        }

                        $calendarEventLanes[$calendarEventKey] = $calendarLane;

                        foreach (array_keys($calendarMeta['days']) as $calendarOccupiedDay) {
                            $calendarLaneDays[$calendarLane][$calendarOccupiedDay] = true;
                        }
                    }

                    $calendarWeekLaneCount = count($calendarLaneDays);
                    $calendarWeekSlots = array_fill(0, count($week), []);

                    foreach ($week as $calendarDayIndex => $calendarDay) {
                        foreach (($calendarDay['events'] ?? []) as $calendarEvent) {
                            $calendarEventKey = (string) (
                                $calendarEvent['id']
                                ?? $calendarEvent['code']
                                ?? md5(json_encode($calendarEvent))
                            );

                            if (! array_key_exists($calendarEventKey, $calendarEventLanes)) {
                                continue;
                            }

                            $calendarLane = $calendarEventLanes[$calendarEventKey];
                            $calendarWeekSlots[$calendarDayIndex][$calendarLane] = $calendarEvent;
                        }
                    }
                @endphp
<div class="week-row">

                    <div class="week-number">

                        <span>
                            S{{ $week[0]['semaine'] }}
                        </span>

                    </div>

                    @foreach ($week as $calendarDayIndex => $day)

                        <div
                            class="
                                day
                                {{ $day['dans_mois'] ? '' : 'outside' }}
                            "
                        >

                            <div
                                class="
                                    day-number
                                    {{ $day['aujourdhui'] ? 'today' : '' }}
                                "
                            >
                                {{ $day['numero'] }}
                            </div>


                            @for (
                                $calendarLane = 0;
                                $calendarLane < $calendarWeekLaneCount;
                                $calendarLane++
                            )
                                @php
                                    $event = $calendarWeekSlots[$calendarDayIndex][$calendarLane] ?? null;
                                @endphp

                                @if ($event !== null)
                                    <div
                                        class="session-slot"
                                        data-calendar-lane="{{ $calendarLane }}"
                                    >

                                @php
                                    /*
                                     * Couleur stable par stage.
                                     *
                                     * Le libellé court génère toujours
                                     * la même teinte, sans avoir besoin
                                     * d'enregistrer une couleur en base.
                                     */
                                    $stageHue =
                                        (
                                            (int) sprintf(
                                                '%u',
                                                crc32(
                                                    (string) (
                                                        $event['stage']
                                                        ?? ''
                                                    )
                                                )
                                            )
                                        )
                                        % 360;
                                @endphp

                                <div
                                    class="
                                        session
                                        {{ $event['complete'] ? 'full' : '' }}
                                    "
                                    style="
                                        --stage-bg:
                                            hsl(
                                                {{ $stageHue }}
                                                80%
                                                95%
                                            );
                                        --stage-border:
                                            hsl(
                                                {{ $stageHue }}
                                                70%
                                                45%
                                            );
                                        --stage-text:
                                            hsl(
                                                {{ $stageHue }}
                                                72%
                                                27%
                                            );
                                        --stage-button-hover:
                                            hsl(
                                                {{ $stageHue }}
                                                72%
                                                37%
                                            );
                                    "
                                >

                                    <div class="session-title">
                                        {{ $event['stage'] }}
                                    </div>

                                    {{-- PORTAIL_LIBELLE_LONG_SURVOL_V1_1 --}}
                                    <div class="stage-long-tooltip">
                                        {{ $event['stage_long'] }}
                                    </div>

                                    @if (
                                        $event['capacite']
                                        !== null
                                    )

                                        @if (
                                            $event['complete']
                                        )

                                            <div class="places full">
                                                Session complète
                                            </div>

                                        @else

                                            <div class="places available">
                                                {{ $event['restantes'] }}
                                                place(s) disponible(s)
                                            </div>

                                        @endif

                                    @endif

                                    {{-- INSCRIPTION_NOUVEL_ONGLET_V1 --}}
                                    <a
                                        href="{{ $event['inscription_url'] }}"
                                        class="register"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >

                                        @if ($event['complete'])
                                            Voir / liste d’attente
                                        @else
                                            Voir / s’inscrire
                                        @endif

                                    </a>

                                </div>


                                    </div>
                                @else
                                    <div
                                        class="session-slot session-slot-empty"
                                        data-calendar-lane="{{ $calendarLane }}"
                                        aria-hidden="true"
                                    ></div>
                                @endif
                            @endfor

                        </div>

                    @endforeach

                </div>

            @endforeach

        </div>

    </div>

    <div class="legend {{ $viewMode === 'calendrier' ? '' : 'calendar-view-hidden' }}">

        <div class="legend-item">

            <span
                class="legend-dot dot-stage"
            ></span>

            Une couleur différente identifie chaque stage.

        </div>

        <div class="legend-item">

            <span
                class="legend-dot dot-full"
            ></span>

            Le texte orange signale une session complète.

        </div>

    </div>

</div>


@if ($viewMode === 'liste')

    <div class="container list-view-container">

        @php
            $sessionsParDate =
                $listSessions
                    ->groupBy(
                        'debut_date'
                    );
        @endphp

        <div class="sessions-list">

            @forelse (
                $sessionsParDate
                as $date =>
                    $sessionsDate
            )

                <section class="sessions-date-group">

                    <div class="sessions-date-title">
                        {{
                            $sessionsDate
                                ->first()[
                                    'debut_date_label'
                                ]
                        }}
                    </div>

                    @foreach (
                        $sessionsDate
                        as $event
                    )

                        <article
                            class="
                                list-session-card
                                {{ $event['complete'] ? 'full' : '' }}
                            "
                        >
                            <div>

                                <div class="list-session-title">
                                    {{ $event['stage'] }}
                                </div>

                                @if (
                                    $event['stage_long']
                                    && $event['stage_long']
                                        !== $event['stage']
                                )
                                    <div class="list-session-long">
                                        {{ $event['stage_long'] }}
                                    </div>
                                @endif

                                <div class="list-session-meta">

                                    <span>
                                        <strong>Session :</strong>
                                        {{ $event['code'] }}
                                    </span>

                                    <span>
                                        <strong>Du :</strong>
                                        {{ $event['debut'] }}
                                    </span>

                                    <span>
                                        <strong>Au :</strong>
                                        {{ $event['fin'] }}
                                    </span>

                                    @if ($event['salle'])
                                        <span>
                                            <strong>Salle :</strong>
                                            {{ $event['salle'] }}
                                        </span>
                                    @endif

                                    @if ($event['fps'])
                                        <span>
                                            <strong>FPS :</strong>
                                            {{ $event['fps'] }}
                                        </span>
                                    @endif

                                    @if (
                                        $event[
                                            'service_responsable'
                                        ]
                                    )
                                        <span>
                                            <strong>
                                                Service responsable :
                                            </strong>
                                            {{
                                                $event[
                                                    'service_responsable'
                                                ]
                                            }}
                                        </span>
                                    @endif

                                </div>

                            </div>

                            <div class="list-session-actions">

                                @if (
                                    $event['capacite']
                                    !== null
                                )
                                    @if ($event['complete'])
                                        <div class="list-places full">
                                            Session complète
                                        </div>
                                    @else
                                        <div class="list-places available">
                                            {{ $event['restantes'] }}
                                            place(s) disponible(s)
                                        </div>
                                    @endif
                                @endif

                                <a
                                    href="{{ $event['inscription_url'] }}"
                                    class="register"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    @if ($event['complete'])
                                        S’inscrire en liste d’attente
                                    @else
                                        S’inscrire
                                    @endif
                                </a>

                            </div>

                        </article>

                    @endforeach

                </section>

            @empty

                <div class="list-empty">
                    @if ($searchTerm !== '')
                        Aucune session programmée
                        ne correspond à
                        « {{ $searchTerm }} ».
                    @else
                        Aucune session à venir.
                    @endif
                </div>

            @endforelse

        </div>

    </div>

@endif

<script>
/* CALENDRIER_ALIGNEMENT_STAGES_V1_HEIGHTS */
(() => {
    const alignCalendarLanes = () => {
        document.querySelectorAll('.week-row').forEach((week) => {
            const slots = Array.from(
                week.querySelectorAll('.session-slot[data-calendar-lane]')
            );

            slots.forEach((slot) => {
                slot.style.height = '';
            });

            const lanes = new Set(
                slots.map((slot) => slot.dataset.calendarLane)
            );

            lanes.forEach((lane) => {
                const laneSlots = slots.filter(
                    (slot) => slot.dataset.calendarLane === lane
                );

                let maxHeight = 0;

                laneSlots.forEach((slot) => {
                    const card = slot.querySelector('.session');

                    if (! card) {
                        return;
                    }

                    maxHeight = Math.max(
                        maxHeight,
                        Math.ceil(card.getBoundingClientRect().height)
                    );
                });

                if (maxHeight <= 0) {
                    return;
                }

                laneSlots.forEach((slot) => {
                    slot.style.height = `${maxHeight}px`;
                });
            });
        });
    };

    window.addEventListener('load', alignCalendarLanes);

    let resizeTimer = null;

    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(alignCalendarLanes, 120);
    });
})();
</script>

@if ($viewMode === 'semaine')

    {{-- PORTAIL_VUE_SEMAINE_V1 --}}
    <div class="container week-view-container">

        <div class="week-toolbar">

            <div class="week-toolbar-actions">
                <a
                    class="button"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semainePrecedente,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                    title="Semaine précédente"
                >
                    ←
                </a>
            </div>

            <div class="week-toolbar-center">
                <div class="week-title">
                    {{ $weekLabel }}
                </div>
            </div>

            <div class="week-toolbar-actions">
                <a
                    class="button"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semaineActuelle,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                >
                    Aujourd’hui
                </a>

                <a
                    class="button"
                    href="{{ $publicRoute(
                        'fpsplanificationstage.public.calendrier',
                        array_filter([
                            'vue' => 'semaine',
                            'semaine' => $semaineSuivante,
                            'q' => $searchTerm !== '' ? $searchTerm : null,
                        ])
                    ) }}"
                    title="Semaine suivante"
                >
                    →
                </a>
            </div>

        </div>

        <div class="week-calendar-scroll">
            <div class="week-calendar">

                <div class="week-days-header">
                    @foreach ($weekDays as $day)
                        <div class="week-day-header">
                            <span class="week-day-name">
                                {{ $day['jour'] }}
                            </span>
                            <span
                                class="week-day-number {{ $day['aujourdhui'] ? 'today' : '' }}"
                            >
                                {{ $day['numero'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @php
                    $weekHasSession = false;
                    $weekRows =
                        isset($weekDays[0]['cells'])
                            ? count($weekDays[0]['cells'])
                            : 0;
                @endphp

                <div class="week-events-grid">
                    @for ($lane = 0; $lane < $weekRows; $lane++)
                        @foreach ($weekDays as $day)
                            @php
                                $event = $day['cells'][$lane] ?? null;
                                if ($event) {
                                    $weekHasSession = true;
                                }
                            @endphp

                            <div class="week-cell">
                                @if ($event)
                                    @php
                                        $stageHue =
                                            (
                                                (int) sprintf(
                                                    '%u',
                                                    crc32(
                                                        (string) (
                                                            $event['stage']
                                                            ?? ''
                                                        )
                                                    )
                                                )
                                            )
                                            % 360;
                                    @endphp

                                    <div
                                        class="session {{ $event['complete'] ? 'full' : '' }}"
                                        title="{{ $event['stage_long'] ?? $event['stage'] }}"
                                        style="
                                            --stage-bg: hsl({{ $stageHue }} 80% 95%);
                                            --stage-border: hsl({{ $stageHue }} 70% 45%);
                                            --stage-text: hsl({{ $stageHue }} 72% 27%);
                                            --stage-button-hover: hsl({{ $stageHue }} 72% 37%);
                                        "
                                    >
                                        <div class="session-title">
                                            {{ $event['stage'] }}
                                        </div>

                                        @if (
                                            ($event['stage_long'] ?? null)
                                            && $event['stage_long'] !== $event['stage']
                                        )
                                            <div class="stage-long-tooltip">
                                                {{ $event['stage_long'] }}
                                            </div>
                                        @endif

                                        @if ($event['capacite'] !== null)
                                            @if ($event['complete'])
                                                <div class="places full">
                                                    Session complète
                                                </div>
                                            @else
                                                <div class="places available">
                                                    {{ $event['restantes'] }}
                                                    place(s) disponible(s)
                                                </div>
                                            @endif
                                        @endif

                                        <a
                                            href="{{ $event['inscription_url'] }}"
                                            class="register"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            @if ($event['complete'])
                                                Voir / liste d’attente
                                            @else
                                                Voir / s’inscrire
                                            @endif
                                        </a>
                                    </div>
                                @else
                                    <div class="week-empty-lane"></div>
                                @endif
                            </div>
                        @endforeach
                    @endfor
                </div>

                @if (! $weekHasSession)
                    <div class="week-no-session">
                        @if ($searchTerm !== '')
                            Aucun stage correspondant à
                            « {{ $searchTerm }} »
                            cette semaine.
                        @else
                            Aucun stage programmé cette semaine.
                        @endif
                    </div>
                @endif

            </div>
        </div>

    </div>

@endif
    </div>

</x-filament-panels::page>
