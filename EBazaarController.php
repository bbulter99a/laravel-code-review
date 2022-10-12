<?php

namespace App\Http\Controllers;

use App\Http\Requests\EBazaarRequest;
use App\Http\Resources\EBazaarResource;
use App\Models\EBazaar;
use App\Models\User;
use App\Repositories\EBazaarRepository;
use Illuminate\Http\Request;

class EBazaarController extends Controller
{
	/**
	 * @var EBazaarRepository
	 */
	protected $repository;

	public function __construct( EBazaarRepository $repository )
	{
		$this->repository = $repository;
	}

	/**
	 * Get Playday Event Pre Requisite
	 * @return Response
	 */
	public function preRequisite()
	{
		return $this->ok( $this->repository->getPreRequisite() );
	}

	public function activity( EBazaar $ebazaar )
	{
		return $this->success( [
			'saved' => $this->repository->activity( $ebazaar )
		] );
	}

	public function livenow()
	{
		return $this->success( [
			'ebazaar' => $this->repository->livenow()
		] );
	}

	public function today()
	{
		return $this->success( [
			'ebazaar' => $this->repository->today()
		] );
	}

	/**
	 * Get all e-bazaars
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$this->authorize( 'view', User::class );

		return $this->repository->paginate();
	}

	public function uploadImage()
	{
		$image = $this->repository->uploadImage();

		return $this->success( [
			'message' => __( 'global.uploaded', [ 'attribute' => 'Image' ] ),
			'image'   => $image
		] );
	}

	public function deleteImage()
	{
		return $this->success( [
			'message' => __( 'global.deleted', [ 'attribute' => 'Image' ] ),
		] );
	}

	public function uploadVideo()
	{
		$video = $this->repository->uploadVideo();

		return $this->success( [
			'message' => __( 'global.uploaded', [ 'attribute' => 'Video' ] ),
			'video'   => $video
		] );
	}


	public function deleteVideo()
	{
		return $this->success( [
			'message' => __( 'global.deleted', [ 'attribute' => 'Video' ] ),
		] );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param EBazaarRequest $request
	 *
	 * @return Response|array
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function store( EBazaarRequest $request )
	{
		$this->authorize( 'create', User::class );

		$ebazaar = $this->repository->create();
		$ebazaar = new EBazaarResource( $ebazaar );

		return $this->success( [
			'message' => __( 'global.added', [ 'attribute' => __( 'ebazaar.ebazaar' ) ] ),
			'ebazaar' => $ebazaar
		] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param EBazaar $ebazaar
	 *
	 * @return EBazaarResource()
	 */
	public function show( EBazaar $ebazaar )
	{
		$this->authorize( 'show', User::class );
		if ( request()->query( 'orders', false ) ) {
			 $ebazaar->loadMissing('orders.customer');
		}

		return new EBazaarResource( $ebazaar );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param EBazaarRequest $request
	 * @param EBazaar $ebazaar
	 *
	 * @return Response|array
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function update( EBazaarRequest $request, EBazaar $ebazaar )
	{
		$this->authorize( 'update', User::class );

		$ebazaar = $this->repository->update( $ebazaar );
		$ebazaar = new EBazaarResource( $ebazaar );

		return $this->success( [
			'message' => __( 'global.updated', [ 'attribute' => __( 'ebazaar.ebazaar' ) ] ),
			'ebazaar' => $ebazaar
		] );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param EBazaar $ebazaar
	 *
	 * @return Response|array
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function destroy( EBazaar $ebazaar )
	{
		$this->authorize( 'delete', User::class );

		$this->repository->delete( $ebazaar );

		return $this->success(
			[ 'message' => __( 'global.deleted', [ 'attribute' => __( 'ebazaar.ebazaar' ) ] ) ]
		);
	}
}
