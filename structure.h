#ifndef PILE_H
# define PILE_H

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#define DBG printf("DEBUG File: %s Line:%d \n",__FILE__,__LINE__);
#define TAILLETDH 27

/** @struct  t_adresse
* @brief Structure d'une adresse
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_adresse
{

	char *adresse;  // le nom de la rue
	char *ville; // la ville
	char *pays; //le pays

}t_adresse;


 /** @struct  t_client
* @brief Structure d'un client
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_client
{
    char cle[11];   // Les Deux premi�res lettres du pr�nom + les deux premi�res du nom + 4 derniers chiffres du num�ro de t�l +  FF si frequent flyer NN si non fr�quent Flyer + 1 chiffre pour �viter les doublons
	char *mot_de_passe;  // le mot de passe pour la gestion (pointeur)

	//Etats civil
	char *nom; 								// le nom du client
	char *prenom; 							// le pr�nom du client
	long tel; 								//num�ro de t�l�phone
	struct t_adresse adresse;				// L'adresse du client
	char *message; 							// les messages �ventuels pour le client
	// Compagnie
	struct t_ff *ff;						// Liens vers Fr�quent Flyer, NULL si pas fr�quent flyer
	struct t_historique_vols *vols;						// Liens vers l'historique des vols en cours , NULL si aucun vols

}t_client;

typedef t_client* ptr_t_client;

typedef struct t_ff* ptr_t_ff;

 /** @struct  t_ff
* @brief Structure pour les fr�quent flyer
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_ff
{
    char cle[11];   // Les Deux premi�res lettres du pr�nom + les deux premi�res du nom + 4 derniers chiffres du num�ro de t�l +  FF si frequent flyer NN si non fr�quent Flyer + 1 chiffre pour �viter les doublons
	long points;  // Les points Fr�quent Flyer acumul�
	long km;  // Les kilom�tres �ffectu�s
	struct t_historique_vols *vols;  // Pointeur vers une liste chain�e des vols �ffectu�s

}t_ff;

/** @struct  t_vols_cours
* @brief Structure pour les vols_en_cours
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_vols_cours
{
    char cle[11];   // Les Deux premi�res lettres du pr�nom + les deux premi�res du nom + 4 derniers chiffres du num�ro de t�l +  FF si frequent flyer NN si non fr�quent Flyer + 1 chiffre pour �viter les doublons
	struct t_historique_vols *vols;  // Pointeur vers une liste chain�e des vols �ffectu�s
}t_vols_cours;

 /** @struct  t_historique_vols
* @brief liste chain�e de vols
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_historique_vols
{
    char num_vol[12];   // le num�ro de vol (desorihhmm) + jour

}t_historique_vols;

 /** @struct  t_avions
* @brief liste chain�e des avions
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_avions
{
    char modele[4];   	// le mod�le de l'avion A380; B747; .....
	long capacite;		// Le nombre de siege dans l'avion
	long autonomie;		// L'autonomie de l'avion ( le nombre de kilom�tre maximale)
}t_avions;

typedef t_avions* ptr_t_avions;  // pointeur vers un avion

/** @struct  t_destination
* @brief liste chain�e des destinations
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_destination
{
    char code[6];   	// le code de destination : 3 premi�res lettres de la destination + 3 premi�res lettres de l'origine
	char *destination;	// Le nom de la destination
	char *origine;		// Le nom de l'origine
	long distance;		// La distance entre les deux

}t_destination;


typedef t_destination* ptr_t_destination;  // pointeur vers une destination


 /** @struct  t_liste_client
* @brief liste chain�e des clients pour un vol rang� par jour.
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_liste_client
{
    char cle_clent[9];   	// le num�ro de cl� du client
	// suiv
    struct t_liste_client *suiv; 					//cellule suivante

}t_liste_client;

typedef t_liste_client* ptr_t_liste_client;  // pointeur vers un avion

/** @struct  t_vols
* @brief arbres ordon�e lexicographiquement des vols
* @version 1.0
* @bug Y a pas
* @warning Y a pas
* @param
*
*/

typedef struct t_vols
{
    char code_vol[10]; // Le code correespondant au vol c'est � partir ce code qu'est rang� l'arbre AVL
	char dest[6]; // Le code correspondant � la destination
	ptr_t_destination ptr_dest;  // pointeur vers la destination
	char avion[4]; // Le code correspondant � l'avion
	ptr_t_avions* ptr_avion;  // pointeur vers l'avion
	// donn�e compl�mentaire
	int horaire; // l'horaire du vol
	int prix; // le prix du vol

	ptr_t_liste_client jour[31];  // un tableau de pointeur chaque case corresepond � un jour du mois et pointe vers la liste des passager
	int nbr_client;  // le nombre de client ayant d�j� r�server ce vol
	// arbres AVL
    struct t_vols *fd; 	//fils droit
    struct t_vols *fg; 	//fils gauche
	int balance;

}t_vols;

typedef t_vols* ptr_t_vols;  // pointeur vers un vols

/** @struct  element
* @brief structure d'une liste chain�e g�n�rale
*
* @param
*
*/

typedef struct element element;
struct element
{
    void *data;
    struct element *suiv;
};

typedef struct donnee
{
    long data;
}donnee;

typedef element* llist;

/** @struct  strucutre
* @brief Une union permettant de stock� n'importe quel pointeur de structure
*
* @param
*
*/
union listeuniversel {
llist  listechaine;

} listeuniversel;
#endif
