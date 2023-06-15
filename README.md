# これはあくまでサンプルです。
これはあくまでどういうコードを書くのか、どういう知識があるのかを判断するためのサンプルです。  
以前bitbucketでバージョン管理しながら自作したシステムのメイン部分のコードをgithubに移植したものになります。vagrant + chefで開発環境を構築して開発していました。  
chefで開発環境構築というのも今更かと思いますので開発環境デプロイ用のコード類を公開する予定は今のところは御座いません。また、バージョン管理自体はbitbucket上で行なっていたためこちらにはコミットログなどは御座いませんが、一般的なgitフローに従って開発していました。  
ドメイン駆動設計で作成されています。4レイヤードアーキテクチャで作成されていますが、PresentationレイヤーはLaravelの機能に完全に任せるようにしているため格納用のディレクトリは存在していません。  
大まかな処理の流れとして、  


リクエスト  
↓  
app/Http/Controllers配下のコントローラーにルーティング  
↓  
コントローラーからapp/Application配下のApplicationクラスのメソッドが実行される  
↓  
Applicationクラスからapp/Domain配下のDomainクラスのメソッドが実行される  
↓  
Domainクラスがapp/Infrastructure配下のクラスが実行されDBなどからデータを取得する  
↓  
コントローラーまで処理結果が返却されてresouces/views配下がレンダリングされる  
↓  
レンダリングされたものをレスポンスとして返却  


という流れになります。  
EloquentはInfrastructure/Eloquentsに格納されています。このシステムではEloquentは完全にDAOとなります。ドメイン駆動設計のフレームワークではDomainレイヤーのEntityに情報を持たせることになっているためです。Eloquentから取得したデータを元にDomainレイヤーの各FactoryがEntityを組み立てて以降の処理で使用しています。そのため一般的なシステムでのEloquentの扱い方とは異なっているかもしれません。  
また、Eloquentにデータの取得処理を依頼するのはDomaiレイヤーのRepositoryになりますが、ドメイン駆動設計ではDomainレイヤーにフレームワークなどの技術的な知識は可能な限り記述しないという規則がある一方で、RepositoryはEloquentとの強依存関係が避けられずDomainレイヤーにRepositoryを実装してしまうと規則違反となるため、DomainレイヤーにはInterfaceを定義して実態はInfrastructure/Repositoriesに実装するという手法を取っています。
