<?php

namespace touiteur\render;

class TagRenderer {
	private string $tag;
	private array $touites;

	public function __construct(string $tag, array $touites){
		$this->tag = $tag;
		$this->touites = $touites;
	}

	public function render(): string {
		$secuTag = filter_var($this->tag, FILTER_SANITIZE_SPECIAL_CHARS);
		$html = "<h2>Touites contenant le tag : #{$secuTag}</h2>\n";
		$html += "<div class='liste-touites'>\n";
		if (empty($this->touites)) {
			$html += "<p>Aucun touite n'a été trouvé pour ce tag.</p>\n";
		} else {
			foreach ($this->touites as $touite) {
				$texte = "SELECT texte FROM touites WHERE ...";  /// a refaire quand la base de donnée sera créée
				$utilisateur = " SELECT utilisateur FROM touites WHERE ..."; /// a refaire quand la base de donnée sera créée
				$secuTexte = filter_var($texte, FILTER_SANITIZE_SPECIAL_CHARS);
				$secuUtilisateur = filter_var($utilisateur, FILTER_SANITIZE_SPECIAL_CHARS);
				$html += "<p class='texte'>{$secuTexte}</p>\n";
				$html += "<div>\n";
				$html += "<span class='utilisateur'>Par <a href='?action=utilisateur&id={$secuUtilisateur}'>{$secuUtilisateur}</a></span>\n";
				$html += "</div>\n";
				$html += "<a class='detail-btn' href='?action=detail&id={$id}'>Voir le touite</a>\n";
				$html += "</article>\n<hr>\n";
			}
		}
		$html += "</div>\n";
		return $html;
	}
}