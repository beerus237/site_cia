<template>
  <Head title="Connexion admin" />

  <div class="page-hero flex min-h-screen flex-col items-center justify-center px-4 py-10">
    <div class="page-hero-orb-1" />
    <div class="page-hero-orb-2" />

    <Link href="/" class="relative mb-8 flex flex-col items-center gap-3">
      <img src="/images/logos/logo-club.jpeg" alt="Logo Club IA ENSPD" class="h-16 w-16 rounded-2xl object-cover shadow-card ring-1 ring-brand-100" />
      <span class="text-sm font-bold uppercase tracking-[0.25em] text-brand-700">Club IA-ENSPD</span>
    </Link>

    <div class="relative w-full overflow-hidden rounded-2xl border border-brand-100 bg-white px-6 py-7 shadow-card-hover sm:max-w-md sm:px-8 sm:py-8">
      <div class="mb-6 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-gradient">
          <LockKeyholeIcon class="h-5.5 w-5.5 text-white" />
        </div>
        <h1 class="text-lg font-bold text-slate-900">Espace administrateur</h1>
        <p class="mt-1 text-sm text-slate-500">Entrez le mot de passe pour accéder au tableau de bord</p>
      </div>

      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="field-label">Mot de passe</label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="passwordFieldType"
              required
              ref="passwordInput"
              class="field-input pr-11"
              placeholder="••••••••"
              @focus="handleFocus"
              @blur="handleBlur"
              @click.stop
              @touchstart.stop
            />
            <button
              type="button"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-brand-600 z-10"
              @pointerdown.prevent="togglePassword"
              @click.stop
              @touchstart.stop
              aria-label="Afficher ou masquer le mot de passe"
            >
              <component 
                :is="showPassword ? EyeOffIcon : EyeIcon" 
                class="h-4.5 w-4.5 pointer-events-none" 
              />
            </button>
          </div>
          <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
        </div>

        <button 
          type="submit" 
          class="btn-primary w-full justify-center" 
          :disabled="form.processing"
          @pointerdown.stop
          @click.stop
        >
          {{ form.processing ? 'Connexion…' : 'Se connecter' }}
        </button>
      </form>
    </div>

    <Link href="/" class="relative mt-6 text-sm font-medium text-slate-500 transition-colors hover:text-brand-600">
      &larr; Retour au site
    </Link>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, nextTick } from 'vue'
import { EyeIcon, EyeOffIcon, LockKeyholeIcon } from 'lucide-vue-next'

// ============ ÉTAT ============
const showPassword = ref(false)
const passwordInput = ref(null)
const isFocused = ref(false)

// ============ FORMULAIRE ============
const form = useForm({
  password: '',
})

// ============ COMPUTED ============
const passwordFieldType = computed(() => {
  return showPassword.value ? 'text' : 'password'
})

// ============ MÉTHODES ============
const submit = () => {
  // Éviter les soumissions multiples
  if (form.processing) return
  
  form.post('/admin/login', {
    onFinish: () => {
      form.reset('password')
    },
  })
}

const togglePassword = (event) => {
  // 🔥 CRUCIAL : Empêcher toute propagation
  if (event) {
    event.preventDefault()
    event.stopPropagation()
    event.stopImmediatePropagation()
  }
  
  // Inverser l'état
  showPassword.value = !showPassword.value
  
  // Restaurer le focus APRÈS le toggle
  nextTick(() => {
    if (passwordInput.value) {
      passwordInput.value.focus()
      // Placer le curseur à la fin
      const length = passwordInput.value.value.length
      passwordInput.value.setSelectionRange(length, length)
    }
  })
}

// Gestionnaires de focus pour éviter les re-rendus
const handleFocus = () => {
  isFocused.value = true
}

const handleBlur = () => {
  isFocused.value = false
}
</script>

<style scoped>
/* 🔥 FIX : Empêcher les animations ou transitions qui pourraient causer des freezes */
input.field-input {
  transition: none !important;
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}

button {
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

/* Si tu as des animations sur le bouton submit, désactive-les temporairement */
.btn-primary {
  transition: none !important;
}

/* Pour les mobiles, éviter tout comportement étrange */
input,
button {
  -webkit-user-select: none;
  user-select: none;
}
</style>
