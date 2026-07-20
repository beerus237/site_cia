<template>
  <PageHero
    eyebrow="Contribuer"
    title="Collecte de données"
    subtitle="Découvrez nos campagnes en cours et contribuez à une intelligence artificielle responsable, adaptée à l'écosystème africain"
  />

  <!-- Introduction -->
  <section class="bg-white py-16">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
      <p v-reveal class="mb-8 text-lg leading-8 text-slate-500">
        <span class="font-semibold text-brand-600">Votre contribution est essentielle</span> pour construire des
        modèles d'IA qui reflètent la diversité et la richesse de notre continent. Chaque campagne ci-dessous répond
        à un besoin précis — choisissez celle qui vous parle.
      </p>
    </div>
  </section>

  <!-- Campagnes actives -->
  <section class="bg-brand-50/50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div v-reveal class="mx-auto mb-16 max-w-2xl text-center">
        <span class="section-eyebrow">Campagnes en cours</span>
        <h2 class="section-title">Choisissez une campagne</h2>
      </div>

      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <Link
          v-for="(campagne, index) in campagnes"
          :key="campagne.id"
          :href="`/collecte-donnees/${campagne.slug}`"
          v-reveal="{ delay: index * 100 }"
          class="surface-card-hover group block overflow-hidden"
        >
          <div class="relative flex h-40 items-center justify-center overflow-hidden bg-brand-gradient">
            <img v-if="campagne.image_url" :src="campagne.image_url" :alt="campagne.titre" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <MegaphoneIcon v-else class="h-12 w-12 text-white/70" />
          </div>

          <div class="p-6">
            <div class="mb-3 flex flex-wrap gap-1.5">
              <span v-for="type in campagne.types_acceptes" :key="type" class="badge-brand">{{ typeLabels[type] }}</span>
            </div>
            <h3 class="mb-2 text-xl font-bold text-slate-900">{{ campagne.titre }}</h3>
            <p class="mb-4 line-clamp-3 text-sm leading-6 text-slate-500">{{ campagne.description }}</p>
            <div class="flex items-center gap-1.5 text-sm font-semibold text-brand-600">
              Contribuer
              <ArrowRightIcon class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
            </div>
          </div>
        </Link>
      </div>

      <div v-if="campagnes.length === 0" class="py-12 text-center">
        <div class="surface-card mx-auto max-w-md p-10">
          <MegaphoneIcon class="mx-auto mb-4 h-12 w-12 text-brand-200" />
          <h3 class="mb-2 text-lg font-bold text-slate-900">Aucune campagne active pour le moment</h3>
          <p class="text-sm text-slate-500">Revenez bientôt, de nouvelles campagnes de collecte seront lancées prochainement.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Confidentialité et éthique -->
  <section class="bg-white py-20">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
      <div v-reveal class="mx-auto mb-12 max-w-2xl text-center">
        <span class="section-eyebrow">
          <ShieldCheckIcon class="h-3.5 w-3.5" />
          Éthique
        </span>
        <h2 class="section-title">Confidentialité &amp; éthique</h2>
      </div>

      <div class="grid gap-8 md:grid-cols-2">
        <div v-reveal class="surface-card p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-gradient">
            <CheckIcon class="h-5.5 w-5.5 text-white" />
          </div>
          <h3 class="mb-3 text-lg font-semibold text-slate-900">Consentement explicite</h3>
          <p class="leading-7 text-slate-500">Toutes les contributions sont soumises à un consentement éclairé et peuvent être retirées à tout moment.</p>
        </div>

        <div v-reveal="{ delay: 100 }" class="surface-card p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-gradient">
            <LockIcon class="h-5.5 w-5.5 text-white" />
          </div>
          <h3 class="mb-3 text-lg font-semibold text-slate-900">Sécurité des données</h3>
          <p class="leading-7 text-slate-500">Chiffrement de bout en bout et stockage sécurisé conforme aux réglementations RGPD et locales.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowRightIcon, CheckIcon, LockIcon, MegaphoneIcon, ShieldCheckIcon } from 'lucide-vue-next'
import PageHero from '@/Components/PageHero.vue'

defineProps({
  campagnes: {
    type: Array,
    default: () => [],
  },
})

const typeLabels = {
  image: 'Images',
  texte: 'Textes',
  dataset: 'Datasets',
}
</script>
