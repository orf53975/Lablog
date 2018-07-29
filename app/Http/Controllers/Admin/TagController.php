<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Tag\Store;
use App\Http\Requests\Tag\Update;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\ArticleTag;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    /**
     * @var Tag
     */
    protected $tag;

    /**
     * TagController constructor.
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * 标签管理列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage()
    {
        $tags = $this->tag->query()->orderBy('id', 'desc')->paginate(10);
        foreach ($tags as $tag) {
            $articleCount = ArticleTag::query()->where('tag_id', $tag->id)->count();
            $tag->article_count = $articleCount;
        }
        return view('admin.tag', compact('tags'));
    }

    /**
     * 标签创建存储.
     *
     * @param  \App\Http\Requests\Tag\Store $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $this->tag->storeData($request->all());
        operation_event(auth()->user()->name,'添加标签');
        // 更新缓存
        Cache::forget('cache:tag_list');
        return redirect()->back();
    }

    /**
     * 标签编辑.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if (is_null($id)) {
            return abort(404, '对不起，找不到相关页面');
        }
        if (!$response = $this->tag->query()->find($id)) {
            return ajax_return(404, ['alert' => '未找到相关数据']);
        }
        return ajax_return(200, $response);
    }

    /**
     * 标签更新.
     *
     * @param  \App\Http\Requests\Tag\Update $request
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request)
    {
        $id = $request->get('id');
        $name=$request->get('edit_name');
        $flag=$request->get('edit_name');
        $this->tag->updateData(['id' => $id], ['name'=>$name,'flag'=>$flag]);
        operation_event(auth()->user()->name,'编辑标签');
        // 更新缓存
        Cache::forget('cache:tag_list');
        return redirect()->back();
    }

    /**
     * 标签删除.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $data = $request->only('tid');
        $arr = explode(',', $data['tid']);
        $map = [
            'id' => ['in', $arr]
        ];
        $this->tag->destroyData($map);
        operation_event(auth()->user()->name,'删除标签');
        // 更新缓存
        Cache::forget('cache:tag_list');
        return redirect()->back();
    }

    public function search(Request $request)
    {
        $keyword = $request->get('keyword');
        $map = [
            ['name', 'like', '%' . $keyword . '%'],
        ];
        $tags = $this->tag->query()->orderBy('id', 'desc')->where($map)->paginate(10);
        foreach ($tags as $tag) {
            $articleCount = ArticleTag::query()->where('tag_id', $tag->id)->count();
            $tag->article_count = $articleCount;
        }
        return view('admin.tag', compact('tags'));
    }
}
